@extends('layouts.app')

@section('content')
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet">

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Electricity Bill</h5>
            <div class="dropdown-list dropdown" role="group">
                <a href="javascript:void(0);" class="dropdown-toggle btn btn-outline-success" data-bs-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-file-excel me-1"></i> Export
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <button class="dropdown-item fs--1 export-excel" data-type="statistics">Statistics</button>
                    <button class="dropdown-item fs--1 export-excel" data-type="detailed">Detailed</button>
                    <button class="dropdown-item fs--1 export-excel" data-type="retailer">Retailer</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="zero-config" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Transaction ID</th>
                        <th>Retailer</th>
                        <th>Board</th>
                        <th>Bill Details</th>
                        <th>Profit & TDS</th>
                        <th>Status</th>
                       
                    </tr>
                </thead>
                <tbody>
                    @foreach($bills as $bill)
                        <tr>
                            <td>{{ $bill->transaction_id ?? 'N/A' }}</td>
                            <td>
                                @if(isset($bill->retailer))
                                    {{ $bill->retailer->name }}<br>
                                    {{ $bill->retailer->mobile }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $bill->board->name ?? 'N/A' }}</td>
                            <td>
                                Account: {{ $bill->bill_no ?? 'N/A' }}<br>
                                Amount: ₹{{ $bill->bill_amount ?? '0.00' }}<br>
                                Date: {{ \Carbon\Carbon::parse($bill->due_date)->format('d-m-Y') }}
                            </td>
                            <td>
                                Profit: ₹{{ $bill->profit ?? '0.00' }}<br>
                                TDS: ₹{{ $bill->tds ?? '0.00' }}
                            </td>
                            <td>{{ $bill->status ?? 'N/A' }}</td>
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- jQuery & DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- DataTables Buttons for Export -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
$(document).ready(function () {
    // Initialize DataTable with export button hidden
    var table = $('#zero-config').DataTable({
        paging: true,
        ordering: true,
        info: true,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Electricity_Bills',
                className: 'd-none',
                exportOptions: {
                    columns: ':visible'
                }
            }
        ],
        initComplete: function () {
            this.api().columns([1, 2]).every(function () {
                var column = this;
                var select = $('<select class="form-select form-select-sm"><option value="">Filter</option></select>')
                    .appendTo($(column.header()).empty())
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                    });

                column.data().unique().sort().each(function (d, j) {
                    var text = $('<div>').html(d).text().trim();
                    if (text.length) {
                        select.append('<option value="' + text + '">' + text + '</option>');
                    }
                });
            });
        }
    });

    // Trigger Excel export when custom export button is clicked
    $(document).on('click', '.export-excel', function () {
        let type = $(this).data('type');
        let title = 'Electricity_Bills_' + type.charAt(0).toUpperCase() + type.slice(1);
        table.button('.buttons-excel').text(title); // optional: set new title
        table.button('.buttons-excel').trigger();
    });
});
</script>
@endsection
