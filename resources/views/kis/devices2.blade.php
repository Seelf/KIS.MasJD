@php
    $myUrn = config('kis.urn');
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Panel sterowania LED – dymek z kolorami
        </h2>
    </x-slot>

    <div class="py-12 flex flex-col items-center space-y-6">
    <input type="hidden" name="device_urn" id="ledDeviceUrn" value="{{ $myUrn }}">

        <div class="relative">
            <canvas id="deviceCanvas" width="300" height="600" class="border shadow-lg rounded-lg"></canvas>

            <div id="colorPopup" class="absolute hidden bg-white p-2 rounded-lg shadow space-x-2 z-50 border border-gray-300">
                <div class="flex space-x-1 mb-2">
                    <button title="Czerwony" style="width: 32px; height: 32px; border-radius: 9999px; background: red; border: 2px solid white; margin-right: 4px;" onclick="selectColor('red')"></button>
                    <button title="Niebieski" style="width: 32px; height: 32px; border-radius: 9999px; background: blue; border: 2px solid white; margin-right: 4px;" onclick="selectColor('blue')"></button>
                    <button title="Zielony" style="width: 32px; height: 32px; border-radius: 9999px; background: green; border: 2px solid white; margin-right: 4px;" onclick="selectColor('green')"></button>
                    <button title="Żółty" style="width: 32px; height: 32px; border-radius: 9999px; background: yellow; border: 2px solid white; margin-right: 4px;" onclick="selectColor('yellow')"></button>
                    <button title="Czarny" style="width: 32px; height: 32px; border-radius: 9999px; background: black; border: 2px solid white; margin-right: 4px;" onclick="selectColor('black')"></button>
                    <button title="Turkus" style="width: 32px; height: 32px; border-radius: 9999px; background: turquoise; border: 2px solid white; margin-right: 4px;" onclick="selectColor('turquoise')"></button>
                    <button title="Fioletowy" style="width: 32px; height: 32px; border-radius: 9999px; background: purple; border: 2px solid white; margin-right: 4px;" onclick="selectColor('purple')"></button>
                    <button title="Biały" style="width: 32px; height: 32px; border-radius: 9999px; background: white; border: 2px solid black; margin-right: 4px;" onclick="selectColor('white')"></button>
                </div>
                <label class="text-sm text-gray-700">
                    <input type="checkbox" id="flashingCheckbox" class="mr-1"> Miganie
                </label>
            </div>
            
        </div>

        <div class="w-1/2">
            <h3 class="text-lg font-semibold mb-2">Status LED:</h3>
            <table class="table-auto w-full border rounded shadow text-center">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2">LED</th>
                        <th class="px-4 py-2">Kolor</th>
                    </tr>
                </thead>
                <tbody id="statusTable" class="bg-white">
                    <tr>
                        <td class="border px-4 py-2 font-semibold">URN</td>
                        <td class="border px-4 py-2" id="status-urn">Brak wybranego</td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2">LED1</td>
                        <td class="border px-4 py-2" id="status-led1">Czerwony</td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2">LED2</td>
                        <td class="border px-4 py-2" id="status-led2">Zielony</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <form id="ledForm" action="{{ route('set.led') }}" method="POST">
            @csrf
            <input type="hidden" name="device_urn" id="ledDeviceUrn">
            <input type="hidden" name="target" id="ledTarget">
            <input type="hidden" name="color" id="ledColor">
            <input type="hidden" name="flashing"  id="ledFlashing" value="false">
        </form>
    </div>

    <script>
        
        const measurements2 = @json($measurements2);
        const configUrn = @json($myUrn);
        const deviceUrnInput = document.querySelector('input[name="device_urn"]');
        const statusUrnCell = document.getElementById('status-urn');
        const hiddenUrnInput = document.getElementById('ledDeviceUrn');

        statusUrnCell.innerText = configUrn || 'Brak wybranego';
        hiddenUrnInput.value = configUrn;
        const canvas = document.getElementById("deviceCanvas");
        const ctx = canvas.getContext("2d");
        const popup = document.getElementById("colorPopup");
        const image = new Image();
        image.src = "{{ asset('images/kisbox.png') }}";

        const latestValues = {
        button1colorkpi: null,
        button2colorkpi: null
    };

    const latestTimestamps = {
        button1colorkpi: null,
        button2colorkpi: null
    };

    measurements2.forEach(m => {
        if (m.key === 'button1Color') {
            if (!latestTimestamps.button1colorkpi || m.info_timestamp > latestTimestamps.button1colorkpi) {
                latestValues.button1colorkpi = m.value;
                latestTimestamps.button1colorkpi = m.info_timestamp;
            }
        }

        if (m.key === 'button2Color') {
            if (!latestTimestamps.button2colorkpi || m.info_timestamp > latestTimestamps.button2colorkpi) {
                latestValues.button2colorkpi = m.value;
                latestTimestamps.button2colorkpi = m.info_timestamp;
            }
        }
    });

    const button1Color = latestValues.button1colorkpi;
    const button2Color = latestValues.button2colorkpi;
    document.getElementById('status-led1').innerText = getColorName(button1Color);
    document.getElementById('status-led2').innerText = getColorName(button2Color);

        const leds = [
            { id: 'led1', x: 149, y: 195, r: 70, color: button1Color },
            { id: 'led2', x: 149, y: 425, r: 70, color: button2Color }
        ];

        let selectedLed = null;

        image.onload = drawCanvas;

        function drawCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
            leds.forEach(led => {
                ctx.beginPath();
                ctx.arc(led.x, led.y, led.r, 0, 2 * Math.PI);
                ctx.fillStyle = led.color;
                ctx.fill();
                ctx.strokeStyle = 'white';
                ctx.lineWidth = 2;
                ctx.stroke();
            });
        }

        canvas.addEventListener("click", function(event) {
            const rect = canvas.getBoundingClientRect();
            const x = event.clientX - rect.left;
            const y = event.clientY - rect.top;

            for (const led of leds) {
                const dx = x - led.x;
                const dy = y - led.y;
                if (Math.sqrt(dx * dx + dy * dy) < led.r) {
                    selectedLed = led;

                    const popupLeft = led.x - 20;
                    const popupTop = led.y + 25;

                    popup.style.left = popupLeft + 'px';
                    popup.style.top = popupTop + 'px';
                    popup.classList.remove('hidden');

                    return;
                }
            }

            popup.classList.add('hidden');
        });

        function selectColor(color) {
            if (!selectedLed) return;

            selectedLed.color = color;
            popup.classList.add('hidden');

            document.getElementById("ledTarget").value = selectedLed.id;
            document.getElementById("ledColor").value = color;

            drawCanvas();
            document.getElementById("ledFlashing").value = document.getElementById("flashingCheckbox").checked ? "true" : "false";
            document.getElementById("ledForm").submit();
        }

        function getColorName(hex) {
            const colorMap = {
                '#ff0000': 'Czerwony',
                '#0000ff': 'Niebieski',
                '#00ff00': 'Zielony',
                '#ffff00': 'Żółty',
                '#000000': 'Czarny',
                '#40e0d0': 'Turkus',
                '#800080': 'Fioletowy',
                '#ffffff': 'Biały'
            };

            const normalizedHex = hex.trim().toLowerCase();
            return colorMap[normalizedHex] || hex;
        }

        document.addEventListener('click', (e) => {
            if (!popup.contains(e.target) && e.target !== canvas) {
                popup.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
