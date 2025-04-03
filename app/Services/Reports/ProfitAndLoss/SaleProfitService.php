<?php
namespace App\Services\Reports\ProfitAndLoss;


use App\Models\Items\ItemTransaction;
use App\Models\Purchase\Purchase;
use App\Services\PaymentTransactionService;
use App\Models\Sale\Sale;
use App\Models\User;

class SaleProfitService{

	private $paymentTransactionService;

	public function __construct(PaymentTransactionService $paymentTransactionService)
    {
        $this->paymentTransactionService = $paymentTransactionService;
    }

    // public function saleProfitTotalAmount($fromDate, $toDate, $warehouseId) {


    //     $sales = Sale::select('id')->whereBetween('sale_date', [$fromDate, $toDate])->get();

    //     // Fetch all relevant transactions for the sale
    //     $saleItemTransactions = ItemTransaction::with('item')
    //         ->when($warehouseId, fn($query) => $query->where('warehouse_id', $warehouseId))
    //         ->whereIn('transaction_id', $sales->pluck('id')) // Use pluck to extract IDs
    //         ->where('transaction_type', 'Sale')
    //         ->get();

    //     // Fetch other transactions like purchases, item opening, etc.
    //     $purchaseItemTransactions = ItemTransaction::when($warehouseId, fn($query) => $query->where('warehouse_id', $warehouseId))
    //         ->whereIn('item_id', $saleItemTransactions->pluck('item_id')->unique())
    //         ->where(function ($query) {
    //             $query->where('transaction_type', getMorphedModelName(Purchase::class))
    //                     ->orWhere('transaction_type', getMorphedModelName('Item Opening'))
    //                     ->orWhere(function ($subQuery) {
    //                         $subQuery->where('transaction_type', getMorphedModelName('Stock Transfer'))
    //                                 ->where('unique_code', 'STOCK_RECEIVE');
    //                     });
    //         })
    //         ->get();


    //     $totalPurchaseCost = 0;
    //     $totalSalePrice = 0;


    //     //Each Sale Item Is Iterated
    //     foreach ($saleItemTransactions as $saleItemTransaction) {
    //         //Purchas Items
    //         $purchaseTotalSum = $purchaseItemTransactions->where('item_id', $saleItemTransaction->item_id)->sum('total');
    //         $purchaseTotalQty = $purchaseItemTransactions->where('item_id', $saleItemTransaction->item_id)->sum('quantity');

    //         // Avoid division by zero
    //         $averagePurchasePrice = $purchaseTotalSum > 0 ? ($purchaseTotalSum / $purchaseTotalQty) : 0;

    //         //Single Sale Item Sale Price
    //         $saleTotalSum = $saleItemTransaction->total - $saleItemTransaction->discount_amount - $saleItemTransaction->tax_amount;
    //         $saleTotalQty = $saleItemTransaction->quantity;

    //         $itemPurchaseCost = $averagePurchasePrice * $saleTotalQty;
    //         $totalPurchaseCost += $itemPurchaseCost;

    //         $totalSalePrice += $saleTotalSum;
    //     }

    //     return [
    //         'totalPurchaseCost' => $totalPurchaseCost,
    //         'totalSalePrice' => $totalSalePrice,
    //     ];

    // }


    // public function saleProfitTotalAmount($fromDate, $toDate, $warehouseId)
    // {
    //     $sales = Sale::select('id')->whereBetween('sale_date', [$fromDate, $toDate])->pluck('id');

    //     // Fetch relevant sale transactions in bulk
    //     $saleItemTransactions = ItemTransaction::with('item')
    //         ->when($warehouseId, fn($query) => $query->where('warehouse_id', $warehouseId))
    //         ->whereIn('transaction_id', $sales)
    //         ->where('transaction_type', 'Sale')
    //         ->get();

    //     // Fetch all relevant purchase transactions grouped by item_id
    //     $purchaseItemTransactions = ItemTransaction::selectRaw('item_id, SUM(total) as total_sum, SUM(quantity) as total_quantity')
    //         ->when($warehouseId, fn($query) => $query->where('warehouse_id', $warehouseId))
    //         ->whereIn('item_id', $saleItemTransactions->pluck('item_id')->unique())
    //         ->where(function ($query) {
    //             $query->where('transaction_type', getMorphedModelName(Purchase::class))
    //                 ->orWhere('transaction_type', getMorphedModelName('Item Opening'))
    //                 ->orWhere(function ($subQuery) {
    //                     $subQuery->where('transaction_type', getMorphedModelName('Stock Transfer'))
    //                             ->where('unique_code', 'STOCK_RECEIVE');
    //                 });
    //         })
    //         ->groupBy('item_id')
    //         ->get()
    //         ->keyBy('item_id'); // Index by item_id for fast lookups

    //     $totalPurchaseCost = 0;
    //     $totalSalePrice = 0;

    //     // Precompute purchase data into an associative array
    //     $purchaseData = [];
    //     foreach ($purchaseItemTransactions as $transaction) {
    //         $purchaseData[$transaction->item_id] = [
    //             'total_sum' => $transaction->total_sum,
    //             'total_quantity' => $transaction->total_quantity,
    //         ];
    //     }

    //     // Iterate over sale transactions
    //     foreach ($saleItemTransactions as $saleItemTransaction) {
    //         $itemId = $saleItemTransaction->item_id;

    //         // Fetch precomputed purchase totals
    //         $purchaseTotalSum = $purchaseData[$itemId]['total_sum'] ?? 0;
    //         $purchaseTotalQty = $purchaseData[$itemId]['total_quantity'] ?? 0;

    //         // Avoid division by zero
    //         $averagePurchasePrice = $purchaseTotalSum > 0 ? ($purchaseTotalSum / $purchaseTotalQty) : 0;

    //         // Calculate sale details
    //         $saleTotalSum = $saleItemTransaction->total - $saleItemTransaction->discount_amount - $saleItemTransaction->tax_amount;
    //         $saleTotalQty = $saleItemTransaction->quantity;

    //         // Update totals
    //         $totalPurchaseCost += $averagePurchasePrice * $saleTotalQty;
    //         $totalSalePrice += $saleTotalSum;
    //     }

    //     return [
    //         'totalPurchaseCost' => $totalPurchaseCost,
    //         'totalSalePrice' => $totalSalePrice,
    //     ];
    // }

    public function saleProfitTotalAmount($fromDate, $toDate, $warehouseId = null)
    {
        // If warehouseId is not provided, fetch warehouses accessible to the user
        $warehouseIds = $warehouseId ? [$warehouseId] : User::find(auth()->id())->getAccessibleWarehouses()->pluck('id');

        // Fetch sale IDs within the date range
        $salesIds = Sale::whereBetween('sale_date', [$fromDate, $toDate])->pluck('id')->toArray();

        // Fetch sale item transactions for the given warehouse(s) and sale IDs
        $saleItemTransactions = ItemTransaction::with('item')
            ->whereIn('warehouse_id', $warehouseIds)
            ->whereIn('transaction_id', $salesIds)
            ->where('transaction_type', 'Sale')
            ->get();

        // Fetch purchase item transactions for the same items
        $purchaseItemTransactions = ItemTransaction::whereIn('warehouse_id', $warehouseIds)
            ->whereIn('item_id', $saleItemTransactions->pluck('item_id')->unique())
            ->where(function ($query) {
                $query->where('transaction_type', getMorphedModelName(Purchase::class))
                    ->orWhere('transaction_type', getMorphedModelName('Item Opening'))
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('transaction_type', getMorphedModelName('Stock Transfer'))
                            ->where('unique_code', 'STOCK_RECEIVE');
                    });
            })
            ->get()
            ->groupBy('item_id');

        $totalPurchaseCost = 0;
        $totalSalePrice = 0;

        foreach ($saleItemTransactions as $saleItemTransaction) {
            $itemModel = $saleItemTransaction->item;

            // Get purchase transactions for the current item
            $itemPurchaseTransactions = $purchaseItemTransactions->get($saleItemTransaction->item_id) ?? collect();

            // Calculate total purchase cost and quantity
            $purchaseTotalSum = $itemPurchaseTransactions->sum('total');
            $purchaseTotalQty = $itemPurchaseTransactions->sum(function ($transaction) use ($itemModel) {
                // Convert secondary units to base units if necessary
                if ($transaction->unit_id == $itemModel->secondary_unit_id && $itemModel->conversion_rate > 0) {
                    return $transaction->quantity / $itemModel->conversion_rate;
                }
                return $transaction->quantity;
            });

            if ($purchaseTotalQty > 0) {
                // Calculate average purchase price per base unit
                $averagePurchasePricePerBaseUnit = $purchaseTotalSum / $purchaseTotalQty;

                // Convert sale quantity to base units if necessary
                $saleQtyInBaseUnits = $saleItemTransaction->quantity;
                if ($saleItemTransaction->unit_id == $itemModel->secondary_unit_id && $itemModel->conversion_rate > 0) {
                    $saleQtyInBaseUnits = $saleItemTransaction->quantity / $itemModel->conversion_rate;
                }

                // Calculate purchase cost based on sale quantity in base units
                $itemPurchaseCost = $averagePurchasePricePerBaseUnit * $saleQtyInBaseUnits;
                $totalPurchaseCost += $itemPurchaseCost;

                // Calculate sale price after deducting discount and tax
                $saleTotalSum = $saleItemTransaction->total - $saleItemTransaction->discount_amount - $saleItemTransaction->tax_amount;
                $totalSalePrice += $saleTotalSum;
            }
        }

        return [
            'totalPurchaseCost' => $totalPurchaseCost,
            'totalSalePrice' => $totalSalePrice,
            
        ];
    }


    public function saleTotalAmount($fromDate, $toDate, $warehouseId){

        //If warehouseId is not provided, fetch warehouses accessible to the user
        $warehouseIds = $warehouseId ? [$warehouseId] : User::find(auth()->id())->getAccessibleWarehouses()->pluck('id');

        $sales = Sale::with(['itemTransaction' => fn($q) => $q->whereIn('warehouse_id', $warehouseIds)])
            ->select('id', 'sale_date')
            ->whereBetween('sale_date', [$fromDate, $toDate])
            ->get();

        if($sales->isNotEmpty()){
            $totalDiscount = $sales->flatMap->itemTransaction->sum('discount_amount') + $sales->sum('round_off');
            $totalNetPrice = $sales->flatMap->itemTransaction->sum('total');
            $totalTax = $sales->flatMap->itemTransaction->sum('tax_amount');
        }

        return [
                'totalDiscount' => $totalDiscount ?? 0,
                'totalNetPrice' => $totalNetPrice ?? 0,
                'totalTax' => $totalTax ?? 0,
        ];
    }

    public function getItemPurchasePriceFromPurchaseEntry($newSaleItemsCollection){
        // Ensure morph map keys are defined
        $this->paymentTransactionService->usedTransactionTypeValue();

        $purchasePriceData = []; // To store adjusted sale price information

        $finalSaleItemsCollection = $newSaleItemsCollection->transform(function ($saleItems) {

            $remainingQuantity  = $saleItems['sale_qty_minus_opening_qty'];

            $saleItems['remaining_quantity'] = 0;

            ItemTransaction::where('transaction_type', 'Purchase')
                ->orderBy('transaction_date')
                ->where('item_id', $saleItems['sale_item_id'])
                ->chunk(30, function ($purchaseItems) use (&$remainingQuantity, &$purchasePriceData) {
                    foreach ($purchaseItems as $transaction) {

                        if ($remainingQuantity <= 0) {
                            break;
                        }
                        $purchasePrice = $transaction->unit_price;

                        $purchaseReturn = ItemTransaction::where('transaction_type', 'Purchase Return')->where('item_id', $transaction->item_id)->get();
                        if($purchaseReturn->count()>0){

                        }

                        if ($transaction->quantity > 0) {
                            if ($transaction->quantity <= $remainingQuantity) {

                                $purchasePriceData[] = [
                                    'transaction_id'    => $transaction->id,
                                    'quantity'          => $transaction->quantity,
                                    'purchase_price'    => $purchasePrice,
                                    'total'             => $transaction->quantity * $purchasePrice,
                                    //'remainingQuantity' => $remainingQuantity - $transaction->quantity,
                                ];
                                $remainingQuantity -= $transaction->quantity;


                            } else {

                                $purchasePriceData[] = [
                                    'transaction_id'    => $transaction->id,
                                    'quantity'          => $remainingQuantity,
                                    'purchase_price'    => $purchasePrice,
                                    'total'             => $remainingQuantity * $purchasePrice,
                                    //'remainingQuantity' => $remainingQuantity - $transaction->quantity,
                                ];
                                $transaction->quantity -= $remainingQuantity;

                                $remainingQuantity = 0;

                            }
                        }


                    }// foreach
                });

            $saleItems['remaining_quantity'] = $purchasePriceData??$saleItems['sale_qty_minus_opening_qty'];

            // After processing, you can calculate profit and loss based on $purchasePriceData
            $totalCost = is_array($purchasePriceData) ? array_sum(array_column($purchasePriceData, 'total')) : 0; // Total cost of adjustments

            $saleItems['remaining_quantity_total_purchase_price'] = $totalCost;

            return $saleItems;
        });//transform end

        return $finalSaleItemsCollection;
    }

}
