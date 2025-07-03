<div class="modal fade" id="recipt-modal" tabindex="-1" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg  modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body" id="printableDiv">
                <div class="mb-6">
                    <table class="w-100">
                        <tbody>
                            <tr>
                                <td>
                                    <img width="150" src="{{ asset('assets/img/bharat-connect.svg') }}" alt="">
                                </td>
                                <td class="text-end">
                                    <img width="150" src="{{ asset('storage/' . $site_settings['logo']) }}" alt="">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <h1 class="modal-title fs-5 mb-2 text-center fw-semibold">Payment Receipt</h1>
                    <table id="bill-details" class="table table-bordered table-sm w-100">
                        <thead>
                            <tr class="bg-gray">
                                <th colspan="3" class="text-center">
                                    <strong class="fw-semibold mb-0">Consumer Details</strong>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-start w-50">Txn Date</td>
                                <td class="created_at w-50"></td>
                            </tr>
                            <tr>
                                <td class="text-start">Consumer Name</td>
                                <td class="consumer_name"></td>
                            </tr>
                            <tr>
                                <td class="text-start">Amount</td>
                                <td class="bill_amount"></td>
                            </tr>
                            <tr>
                                <td class="text-start">K No</td>
                                <td class="consumer_no"></td>
                            </tr>
                            <tr>
                                <td class="text-start">Txn No.</td>
                                <td class="transaction_id"></td>
                            </tr>
                            <tr>
                                <td class="text-start">Service</td>
                                <td class="type"></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered table-sm w-100">
                        <thead>
                            <tr class="bg-gray">
                                <th colspan="3" class="text-center">
                                    <strong class="fw-semibold mb-0">Kiosk Details</strong>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="w-50 fw-semibold">Kiosk Name</td>
                                <td class="w-50 " colspan="2">{{ auth()->user()->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Kiosk Mobile</td>
                                <td colspan="2">{{ auth()->user()->mobile }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Kiosk Address</td>
                                <td colspan="2">{{ auth()->user()->address ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Disclaimer and Note -->
                <div class="text-sm text-gray-700">
                    <p><strong class="text-dark">Disclaimer:</strong> Thank you for your payment. Your payment will be updated at the biller's end within 2-3 working days. If you have paid your bill before the due date, no late payment fees would be charged to your bill. If you have paid your bill partially, you could be charged a late payment fees by the biller. Any excess payment made would be adjusted with the next bill due. Partial payments would be liable to late payment fees.</p>
                    <p><strong class="text-dark">Note:</strong> <span class="font-semibold">This is computer generated invoice no physical signature required.</span></p>
                </div>
            </div>
            <div class="modal-footer">
                <a href="" class="btn btn-primary" target="_blank" id="receipt">Download</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>