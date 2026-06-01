<?php
// app/Http/Controllers/InvoiceController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Display list of invoices
     */
    public function index()
    {
        $user = Auth::user();
        $invoices = $user->invoices();
        
        return view('invoices.index', compact('invoices'));
    }
    
    /**
     * Download invoice as PDF
     */
    public function download($invoiceId)
    {
        $user = Auth::user();
        
        try {
            return $user->downloadInvoice($invoiceId, [
                'vendor' => config('app.name'),
                'product' => 'Subscription',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('invoices.index')
                ->with('error', 'Invoice not found.');
        }
    }
}