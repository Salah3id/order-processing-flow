<?php

namespace App\Http\Controllers\Order;

use App\Events\OrderCreated;
use App\Exceptions\DataRaceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessOrderRequest;
use App\Http\Resources\OrderResource;
use App\Interfaces\OrderRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class ProcessOrderController extends Controller
{

    private $maxRaceRetries = 3;
    private $retryRaceCount = 0;

    public function __construct(private OrderRepositoryInterface $orderRepository) 
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(ProcessOrderRequest $request)
    {
        // Optimistic locking using versioning and retry mechanism with DataRaceException handling.
        do {

            DB::beginTransaction();
            try {

                // Retrieve the original version of used ingredients to handle concurrency issues by using versioning of timestamp 
                $ingredientsOriginalVersion = $this->orderRepository->getIngredients($request->products);

                // This validation utilizes the IngredientsAvailableInStock rule
                $request->validate($request->rules());

                // Persist the Order in the database
                $order = $this->orderRepository->createWithProducts($request->products);
                
                // Update the stock of the ingredients validated atomically with optimistic locking inside
                $this->orderRepository->updateIngredientsSafely($order, $ingredientsOriginalVersion);

                event(new OrderCreated($order));
                
                DB::commit();

                $order->refresh();

                return new OrderResource($order->with(['products'])->first());            
                
            } catch (Exception $e) 
            {
                DB::rollBack();
                
                if (!$e instanceof DataRaceException) {
                    throw $e;
                    break;
                } else {
                    $this->retryRaceCount++;
                }
                
            } 

        } while($this->retryRaceCount < $this->maxRaceRetries);
    }
}
