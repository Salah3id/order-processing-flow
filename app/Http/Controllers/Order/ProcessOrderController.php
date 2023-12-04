<?php

namespace App\Http\Controllers\Order;

use App\Events\OrderCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessOrderRequest;
use App\Http\Resources\OrderResource;
use App\Interfaces\OrderRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class ProcessOrderController extends Controller
{

    public function __construct(private OrderRepositoryInterface $orderRepository) 
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(ProcessOrderRequest $request)
    {
        DB::beginTransaction();

        try {

            // Retrieve the original version of used ingredients to handle concurrency issues by using versioning of timestamp 
            $ingredientsOriginalVersion = $this->orderRepository->getIngredients($request->products);

            // This validation utilizes the IngredientsAvailableInStock rule
            // to check that the requested quantities of ingredients are within the available stock levels.
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
            throw $e;

        } 
    }
}
