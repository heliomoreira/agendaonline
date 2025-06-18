<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .booking-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
        .form-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="text-center">
        <img src="{{asset('logo.png')}}" alt="Logo" class="logo">
    </div>

    <div class="booking-container">
        <h2 class="form-title text-center">Book Your Appointment</h2>

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function loadAvailableSlots() {
        const serviceId = document.getElementById('service_id').value;
        const professionalId = document.getElementById('professional_id').value;
        const day = document.getElementById('day').value;

        const timeSelect = document.getElementById('time_select');
        timeSelect.innerHTML = '<option>Loading...</option>';

        if (!serviceId || !day) return;

        const params = new URLSearchParams({
            service_id: serviceId,
            start_date: day,
            end_date: day
        });
        if (professionalId) params.append('professional_id', professionalId);

        fetch(`/available-slots?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                timeSelect.innerHTML = '<option value="">Select a time...</option>';
                const slots = [];

                data.forEach(entry => {
                    entry.available_slots.forEach(time => {
                        slots.push({
                            time: time,
                            professional_id: entry.professional_id,
                            professional_name: entry.professional_name
                        });
                    });
                });

                if (slots.length === 0) {
                    timeSelect.innerHTML = '<option>No available slots</option>';
                    return;
                }

                slots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot.time;
                    option.text = `${slot.time} (${slot.professional_name})`;
                    option.setAttribute('data-professional-id', slot.professional_id);
                    timeSelect.appendChild(option);
                });
            })
            .catch(err => {
                console.error(err);
                timeSelect.innerHTML = '<option>Error loading slots</option>';
            });
    }

    document.getElementById('service_id').addEventListener('change', loadAvailableSlots);
    document.getElementById('professional_id').addEventListener('change', loadAvailableSlots);
    document.getElementById('day').addEventListener('change', loadAvailableSlots);
</script>
</body>
</html>
