<div class="form-group patient-assign-widget">
    <label>Search Patient</label>
    <div class="input-group mb-2">
        <input type="text" class="form-control patient-search-input" placeholder="Search by name, phone or ward..." autocomplete="off">
        <div class="input-group-append">
            <button type="button" class="btn btn-info patient-search-btn"><i class="fas fa-search"></i></button>
        </div>
    </div>
    <ul class="list-group mb-2 patient-search-results d-none"></ul>
    <div class="patient-selected">
        <span class="patient-chip badge badge-info px-3 py-2 d-none" data-id="">
            <i class="fas fa-user mr-1"></i>
            <span class="patient-chip-name"></span>
            <button type="button" class="patient-chip-remove border-0 bg-transparent text-white p-0 ml-1" style="line-height:1" title="Remove">&times;</button>
            <input type="hidden" name="patient_id" id="patient_id" value="">
        </span>
        <small class="form-text text-muted patient-empty-hint">
            No patient selected. Search above to find a patient.
        </small>
    </div>
</div>

@push('js')
<script>
    $(function () {
        var $widget = $('.patient-assign-widget');
        var $input = $widget.find('.patient-search-input');
        var $results = $widget.find('.patient-search-results');
        var $selectedBox = $widget.find('.patient-selected');
        var $chip = $widget.find('.patient-chip');
        var $hiddenInput = $widget.find('#patient_id');
        var $emptyHint = $widget.find('.patient-empty-hint');

        function selectPatient(id, name) {
            $hiddenInput.val(id);
            $chip.find('.patient-chip-name').text(name);
            $chip.data('id', id);
            $chip.removeClass('d-none');
            $emptyHint.addClass('d-none');
            $results.addClass('d-none').empty();
            $input.val('');

            // Trigger the existing patient_id change handler on the form
            // so balance panel, amount auto-fill, etc. all update
            var $formSelect = $('#patient_id');
            if ($formSelect.length) {
                // swap the real select's value too so form submission works
                $formSelect.val(id).trigger('change');
            } else {
                // no existing select — trigger our own change handler
                $chip.trigger('patientSelected', [id]);
            }
        }

        function removePatient() {
            var oldId = $hiddenInput.val();
            $hiddenInput.val('');
            $chip.addClass('d-none').data('id', '');
            $chip.find('.patient-chip-name').text('');
            $emptyHint.removeClass('d-none');

            var $formSelect = $('#patient_id');
            if ($formSelect.length) {
                $formSelect.val('').trigger('change');
            } else {
                $chip.trigger('patientDeselected', [oldId]);
            }
        }

        $chip.find('.patient-chip-remove').on('click', removePatient);

        function runSearch(q) {
            if (!q) {
                $results.addClass('d-none').empty();
                return;
            }

            $.getJSON('{{ route('patients.search') }}', { q: q }, function (data) {
                $results.removeClass('d-none').empty();

                if (!data.length) {
                    $results.append('<li class="list-group-item text-muted">No matching patients found.</li>');
                    return;
                }

                $.each(data, function (i, pt) {
                    var info = pt.ward ? ' — ' + pt.ward : '';
                    var rate = pt.amount_to_pay ? ' [' + parseFloat(pt.amount_to_pay).toLocaleString() + '/day]' : '';
                    $('<li class="list-group-item list-group-item-action patient-result" style="cursor:pointer;"></li>')
                        .append('<i class="fas fa-user mr-2 text-info"></i>')
                        .append(document.createTextNode(pt.name + info + rate))
                        .on('click', function () { selectPatient(pt.id, pt.name); })
                        .appendTo($results);
                });
            });
        }

        var debounce;
        $input.on('input', function () {
            clearTimeout(debounce);
            debounce = setTimeout(function () { runSearch($input.val().trim()); }, 300);
        });
        $widget.find('.patient-search-btn').on('click', function () {
            runSearch($input.val().trim());
        });
        $(document).on('click', function (e) {
            if (!$widget.is(e.target) && $widget.has(e.target).length === 0) {
                $results.addClass('d-none');
            }
        });
    });
</script>
@endpush