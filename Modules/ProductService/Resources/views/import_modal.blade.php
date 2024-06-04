<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <div id="process_area" class="overflow-auto import-data-table">
            </div>
        </div>
        <div class="form-group col-12 d-flex justify-content-end col-form-label">
            <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
            <button type="submit" name="import" id="import" class="btn btn-primary ms-2" disabled>{{__('Import')}}</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var total_selection = 0;

        var first_name = 0;

        var last_name = 0;

        var email = 0;

        var column_data = [];

        $(document).on('change', '.set_column_data', function() {
            var column_name = $(this).val();

            var column_number = $(this).data('column_number');

            if (column_name in column_data) {

                toastrs('Error', 'You have already define ' + column_name + ' column', 'error');

                $(this).val('');
                return false;
            }
            if (column_name != '') {
                column_data[column_name] = column_number;
            } else {
                const entries = Object.entries(column_data);

                for (const [key, value] of entries) {
                    if (value == column_number) {
                        delete column_data[key];
                    }
                }
            }

            total_selection = Object.keys(column_data).length;
            if (total_selection == 12) {
                $("#import").removeAttr("disabled");
                sku = column_data.sku;
                name = column_data.name;
                category = column_data.category;
                colour = column_data.colour;
                fuel = column_data.fuel;
                mfg_year = column_data.mfg_year;
                engine_cc = column_data.engine_cc;
                engine_no = column_data.engine_no;
                sale_price = column_data.sale_price;
                bid_no = column_data.bid_no;
                bid_date = column_data.bid_date;
                vehicle_status = column_data.vehicle_status;
            } else {
                $('#import').attr('disabled', 'disabled');
            }

        });

        $(document).on('click', '#import', function(event) {

            event.preventDefault();

            $.ajax({
                url: "{{ route('product-service.import.data') }}",
                method: "POST",
                data: {
                    sku: sku,
                    name: name,
                    category: category,
                    colour: colour,
                    fuel: fuel,
                    mfg_year: mfg_year,
                    engine_cc: engine_cc,
                    engine_no: engine_no,
                    sale_price: sale_price,
                    bid_no: bid_no,
                    bid_date: bid_date,
                    vehicle_status: vehicle_status,
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    $('#import').attr('disabled', 'disabled');
                    $('#import').text('Importing...');
                },
                success: function(data) {
                    $('#import').attr('disabled', false);
                    $('#import').text('Import');
                    $('#upload_form')[0].reset();

                    if (data.html == true) {
                        $('#process_area').html(data.response);
                        $("button").hide();
                        toastrs('Error', 'These data are not inserted', 'error');

                    } else {
                        $('#message').html(data.response);
                        $('#commonModalOver').modal('hide')
                        toastrs('Success', data.response, 'success');
                        location.reload();
                    }

                }
            })

        });
    });
</script>
