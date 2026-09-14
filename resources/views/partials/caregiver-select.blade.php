<div class="form-group caregiver-select-widget">
    <label>Search Caregiver</label>
    <div class="input-group mb-2">
        <input type="text" class="form-control caregiver-search-input" placeholder="Search by name, phone or NIN..." autocomplete="off">
        <div class="input-group-append">
            <button type="button" class="btn btn-info caregiver-search-btn"><i class="fas fa-search"></i></button>
        </div>
    </div>
    <ul class="list-group mb-2 caregiver-search-results d-none"></ul>
    <div class="caregiver-selected">
        <span class="caregiver-chip badge badge-info px-3 py-2 d-none" data-id="" data-rate="" data-daily-rate="" data-payment-plan="">
            <i class="fas fa-user-nurse mr-1"></i>
            <span class="caregiver-chip-name"></span>
            <button type="button" class="caregiver-chip-remove border-0 bg-transparent text-white p-0 ml-1" style="line-height:1" title="Remove">&times;</button>
            <input type="hidden" name="caregiver_id" id="caregiver_id_widget" value="">
        </span>
        <small class="form-text text-muted caregiver-empty-hint">
            No caregiver selected. Search above to find a caregiver.
        </small>
    </div>
</div>

@push('js')
<script>
    $(function () {
        var $widget = $('.caregiver-select-widget');
        var $input = $widget.find('.caregiver-search-input');
        var $results = $widget.find('.caregiver-search-results');
        var $chip = $widget.find('.caregiver-chip');
        var $hiddenInput = $widget.find('#caregiver_id_widget');
        var $emptyHint = $widget.find('.caregiver-empty-hint');

        function selectCaregiver(id, name, rate, paymentPlan) {
            var monthlyRate = parseFloat(rate) || 0;
            var dailyRate = (paymentPlan === 'monthly') ? monthlyRate / 30 : monthlyRate;

            $hiddenInput.val(id);
            $chip.data('id', id);
            $chip.data('rate', monthlyRate);
            $chip.data('daily-rate', dailyRate);
            $chip.data('payment-plan', paymentPlan || 'daily');
            $chip.find('.caregiver-chip-name').text(name);
            $chip.removeClass('d-none');
            $emptyHint.addClass('d-none');
            $results.addClass('d-none').empty();
            $input.val('');

            $chip.trigger('caregiverSelected', [id, monthlyRate, dailyRate, paymentPlan]);
        }

        function removeCaregiver() {
            var oldId = $hiddenInput.val();
            $hiddenInput.val('');
            $chip.addClass('d-none').data('id', '').data('rate', '').data('daily-rate', '').data('payment-plan', '');
            $chip.find('.caregiver-chip-name').text('');
            $emptyHint.removeClass('d-none');
            $chip.trigger('caregiverDeselected', [oldId]);
        }

        $chip.find('.caregiver-chip-remove').on('click', removeCaregiver);

        function runSearch(q) {
            if (!q) {
                $results.addClass('d-none').empty();
                return;
            }

            $.getJSON('{{ route('caregivers.search') }}', { q: q }, function (data) {
                $results.removeClass('d-none').empty();

                if (!data.length) {
                    $results.append('<li class="list-group-item text-muted">No matching caregivers found.</li>');
                    return;
                }

                $.each(data, function (i, cg) {
                    var info = cg.phone ? ' — ' + cg.phone : '';
                    $('<li class="list-group-item list-group-item-action caregiver-result" style="cursor:pointer;"></li>')
                        .append('<i class="fas fa-user-nurse mr-2 text-info"></i>')
                        .append(document.createTextNode(cg.name + info))
                        .on('click', function () {
                            selectCaregiver(cg.id, cg.name, cg.monthly_rate, cg.payment_plan);
                        })
                        .appendTo($results);
                });
            });
        }

        var debounce;
        $input.on('input', function () {
            clearTimeout(debounce);
            debounce = setTimeout(function () { runSearch($input.val().trim()); }, 300);
        });
        $widget.find('.caregiver-search-btn').on('click', function () {
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