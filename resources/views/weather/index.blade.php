<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Weather Dashboard</title>

    <!-- ✅ Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .weather-card {
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 8px;
            padding: 25px;
            margin-top: 20px;
            background-color: white;
        }
        .error {
            color: red;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="text-center mb-4">🌤️ Weather Dashboard</h2>

    <div class="row justify-content-center mb-4">
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" id="city" class="form-control" placeholder="Enter city name">
                <button class="btn btn-primary" onclick="getWeather()">Get Weather</button>
            </div>
        </div>
    </div>

    <div id="weather" class="row justify-content-center">
        <div class="col-md-8 text-center">
            <p class="text-muted">Enter a city name to view current weather and 5-day forecast.</p>
        </div>
    </div>
</div>

<!-- ✅ Bootstrap JS (for optional features) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    async function getWeather() {
        const city = document.getElementById('city').value.trim();
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const weatherDiv = document.getElementById('weather');

        if (!city) {
            weatherDiv.innerHTML = `<div class="col-md-8"><p class="error">Please enter a city name.</p></div>`;
            return;
        }

        weatherDiv.innerHTML = `<div class="col-md-8 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>`;

        try {
            const response = await fetch('/weather', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ city })
            });

            const data = await response.json();

            if (data.error) {
                weatherDiv.innerHTML = `<div class="col-md-8"><p class="error">${data.error}</p></div>`;
                return;
            }

            const current = data.current;
            const forecastList = data.forecast.list;

            let html = `
                <div class="col-md-8">
                    <div class="weather-card">
                        <h4 class="mb-3">Current Weather in ${current.name}</h4>
                        <p><strong>🌡 Temperature:</strong> ${current.main.temp} °C</p>
                        <p><strong>💧 Humidity:</strong> ${current.main.humidity}%</p>
                        <p><strong>🌥 Condition:</strong> ${current.weather[0].description}</p>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="weather-card">
                        <h5>📅 5-Day Forecast</h5>
                        <ul class="list-group list-group-flush mt-3">
            `;

            forecastList.filter((_, i) => i % 8 === 0).forEach(item => {
                const date = new Date(item.dt_txt);
                html += `
                    <li class="list-group-item">
                        <strong>${date.toDateString()}</strong>: ${item.main.temp} °C, ${item.weather[0].description}
                    </li>
                `;
            });

            html += `
                        </ul>
                    </div>
                </div>
            `;

            weatherDiv.innerHTML = html;

        } catch (error) {
            weatherDiv.innerHTML = `<div class="col-md-8"><p class="error">Something went wrong. Please try again.</p></div>`;
            console.error(error);
        }
    }
</script>

</body>
</html>
