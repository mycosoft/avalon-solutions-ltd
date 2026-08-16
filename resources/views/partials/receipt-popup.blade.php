@if(session('receipt_payment_id'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var receiptId = {{ (int) session('receipt_payment_id') }};
            if (!receiptId) return;

            // Try to open the POS thermal receipt as a popup window (auto-print).
            var receiptUrl = "{{ url('/payments/' . session('receipt_payment_id') . '/receipt') }}";
            var popup = window.open(
                receiptUrl,
                'pos_receipt_' + receiptId,
                'width=420,height=720,menubar=no,toolbar=no,location=no,status=no,resizable=yes,scrollbars=yes'
            );

            // If popup was blocked, fallback to a prominent toast so user can click.
            if (!popup || popup.closed || typeof popup.closed === 'undefined') {
                showReceiptFallback(receiptUrl);
            } else {
                // Focus the popup once it loads.
                popup.focus();
            }
        });

        function showReceiptFallback(url) {
            var toast = document.createElement('div');
            toast.setAttribute('id', 'receipt-fallback-toast');
            toast.style.cssText = [
                'position: fixed',
                'right: 20px',
                'bottom: 20px',
                'z-index: 99999',
                'background: #17a2b8',
                'color: #fff',
                'padding: 16px 22px',
                'border-radius: 10px',
                'box-shadow: 0 8px 24px rgba(0,0,0,0.25)',
                'display: flex',
                'align-items: center',
                'gap: 14px',
                'max-width: 360px',
                'font-family: inherit'
            ].join(';');

            toast.innerHTML = `
                <i class="fas fa-receipt" style="font-size: 1.4rem;"></i>
                <div style="flex:1;">
                    <div style="font-weight:600;margin-bottom:2px;">Payment recorded</div>
                    <div style="font-size:0.85rem;opacity:0.9;">Receipt popup was blocked by your browser.</div>
                </div>
                <a href="${url}" target="_blank"
                   style="background:#fff;color:#17a2b8;padding:6px 12px;border-radius:6px;
                          text-decoration:none;font-weight:600;font-size:0.85rem;">
                    Open Receipt
                </a>
                <button type="button" onclick="this.parentNode.remove()"
                        style="background:transparent;color:#fff;border:none;font-size:1.1rem;cursor:pointer;padding:0 4px;">
                    <i class="fas fa-times"></i>
                </button>
            `;
            document.body.appendChild(toast);

            setTimeout(function() {
                if (toast && toast.parentNode) toast.remove();
            }, 12000);
        }
    </script>
@endif