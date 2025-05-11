document.addEventListener('DOMContentLoaded', function() {
    const bookButton = document.querySelector('.book-now-btn');
    if (bookButton) {
        bookButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get package details from the button's data attributes
            const packageId = this.dataset.packageId;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const comments = document.getElementById('comments')?.value || '';
            
            // Create form data
            const formData = new FormData();
            formData.append('package_id', packageId);
            formData.append('start_date', startDate);
            formData.append('end_date', endDate);
            formData.append('comments', comments);
            
            // Create Razorpay order
            fetch('create-razorpay-order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                // Initialize Razorpay
                const options = {
                    key: data.key_id,
                    amount: data.amount,
                    currency: data.currency,
                    name: "Tour Booking",
                    description: "Tour Package Payment",
                    order_id: data.order_id,
                    handler: function(response) {
                        // Create form data for verification
                        const verifyData = new FormData();
                        verifyData.append('payment_id', response.razorpay_payment_id);
                        verifyData.append('order_id', response.razorpay_order_id);
                        verifyData.append('signature', response.razorpay_signature);
                        
                        // Verify payment
                        fetch('verify-payment.php', {
                            method: 'POST',
                            body: verifyData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Booking confirmed successfully! Your booking ID is: ' + data.booking_id);
                                window.location.href = 'booking-history.php';
                            } else {
                                alert('Payment verification failed: ' + data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while verifying payment');
                        });
                    },
                    modal: {
                        ondismiss: function() {
                            console.log('Checkout form closed');
                        }
                    },
                    theme: {
                        color: "#3399cc"
                    }
                };
                
                const rzp = new Razorpay(options);
                rzp.on('payment.failed', function(response) {
                    alert('Payment failed: ' + response.error.description);
                });
                rzp.open();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating payment order');
            });
        });
    }
}); 