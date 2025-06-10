@php
    $myUrn = config('kis.urn');
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Tryb czatu LED (Messenger)
        </h2>
    </x-slot>

    <div class="py-12 max-w-xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-4">
            <p class="text-white text-sm">URN urządzenia: <strong>{{ $myUrn }}</strong></p>
            <input type="hidden" name="device_urn" value="{{ $myUrn }}" id="ledDeviceUrn">

            <div id="chatWindow" class="h-64 overflow-y-auto border rounded p-4 bg-gray-100 space-y-2 text-sm">
                <!-- Wiadomości będą dodawane dynamicznie -->
            </div>

            <form id="chatForm" class="flex space-x-2 items-center">
                @csrf
                <input type="text" id="chatInput" class="flex-1 border rounded px-3 py-1" placeholder="Wpisz komendę..." />
                <button type="button" id="emojiBtn" class="text-gray-500 hover:text-gray-800 text-xl">😊</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600">Wyślij</button>
            </form>

            <div id="emojiPicker" class="absolute z-50 hidden">
                <emoji-picker></emoji-picker>
            </div>
        </div>
    </div>

    <form id="ledForm" action="{{ route('set.led') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="device_urn" id="ledDeviceUrn2" />
        <input type="hidden" name="target" id="ledTarget" />
        <input type="hidden" name="color" id="ledColor" />
        <input type="hidden" name="flashing" id="ledFlashing" value="false" />
    </form>

    <!-- Emoji Picker Library -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

    <script>
 
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatWindow = document.getElementById('chatWindow');

    const urnInput = document.getElementById('ledDeviceUrn');
    const targetInput = document.getElementById('ledTarget');
    const colorInput = document.getElementById('ledColor');
    const flashingInput = document.getElementById('ledFlashing');

    const emojiBtn = document.getElementById('emojiBtn');
    const emojiPicker = document.getElementById('emojiPicker');

    const savedUrn = localStorage.getItem('selectedUrn');

    const staticUrn = "{{ config('kis.urn') }}";
    urnInput.value = staticUrn;

    function addMessage(text, sender = 'user') {
        const msg = document.createElement('div');
        msg.className = sender === 'user' ? 'text-right' : 'text-left text-blue-700';
        msg.innerText = text;
        chatWindow.appendChild(msg);
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    function sendLedCommand(target, color, flashing = false) {
        const payload = {
            device_urn: urnInput.value,
            target,
            color,
            flashing: flashing ? 'true' : 'false',
            _token: document.querySelector('input[name="_token"]').value
        };

        fetch("{{ route('set.led') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": payload._token
            },
            body: JSON.stringify(payload)
        }).then(res => res.json())
        .then(data => console.log("Odpowiedź LED:", data))
        .catch(error => console.error("Błąd LED:", error));
    }

    let commands = {};
    let learning = null;
    let learningSteps = [];
    let stepIndex = 0;
    let duo = false;

    fetch("{{ asset('data/commands.json') }}")
        .then(res => res.json())
        .then(data => commands = data);

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const input = chatInput.value.trim();
        const lower = input.toLowerCase();
        if (!input) return;

        addMessage(input, 'user');

        if (lower.startsWith('zapomnij ')) {
            const cmdToForget = lower.replace('zapomnij ', '').trim();

            if (commands[cmdToForget]) {
                delete commands[cmdToForget];

                fetch('/save-commands', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ commands })
                })
                .then(() => addMessage(`Zapomniałem komendy: "${cmdToForget}" 🧠❌`, 'bot'))
                .catch(() => addMessage('Nie udało się zapomnieć tej komendy 😢', 'bot'));
            } else {
                addMessage(`Nie znam komendy "${cmdToForget}"`, 'bot');
            }

            chatInput.value = '';
            return;
        }

        if (learning !== null) {
            handleLearningStep(lower);
            chatInput.value = '';
            return;
        }

        if (commands[lower]) {
            const cmd = commands[lower];
            addMessage(cmd.message, 'bot');
            cmd.actions.forEach(action => {
                sendLedCommand(action.target, action.color, action.flashing);
            });
        } else {
            addMessage('Nie znam tej komendy 😢. Chcesz mnie jej nauczyć? (tak/nie)', 'bot');
            learning = { command: lower };
            stepIndex = -1;
        }

        chatInput.value = '';
    });

    function handleLearningStep(input) {
        if (stepIndex === -1) {
            if (input === 'tak') {
                learningSteps = [
                    'Jaką wiadomość bot powinien odpowiedzieć?',
                    'Który LED ustawić? (led1, led2, oba)',
                    'Jaki kolor led1?',
                    'Jaki kolor led2?',
                    'Czy led1 ma migać? (tak/nie)',
                    'Czy led2 ma migać? (tak/nie)'
                ];
                learning.response = '';
                learning.actions = [];
                stepIndex = 0;
                addMessage(learningSteps[stepIndex], 'bot');
            } else {
                addMessage('Okej, nie uczę się tej komendy 😊', 'bot');
                learning = null;
            }
            return;
        }

        switch (stepIndex) {
            case 0:
                learning.response = input;
                stepIndex = 1;
                break;
            case 1:
                if (input === 'oba') {
                    learning.actions.push({ target: 'led1' }, { target: 'led2' });
                    duo = true;
                    stepIndex = 2;
                } else if (input === 'led1' || input === 'led2') {
                    learning.actions.push({ target: input });
                    duo = false;
                    stepIndex = input === 'led1' ? 2 : 3;
                } else {
                    addMessage('Wpisz: led1, led2 albo oba.', 'bot');
                }
                break;
            case 2:
                const led1 = learning.actions.find(a => a.target === 'led1');
                if (led1) { led1.color = input.toLowerCase(); }
                else { addMessage('Wystąpił błąd spróbuj ponownie później'); }
                stepIndex = duo ? 3 : 4;
                break;
            case 3:
                const led2 = learning.actions.find(a => a.target === 'led2');
                if (led2) { led2.color = input.toLowerCase(); }
                else { addMessage('Wystąpił błąd spróbuj ponownie później'); }
                stepIndex = duo ? 4 : 5;
                break;
            case 4:
                const led1Flash = learning.actions.find(a => a.target === 'led1');
                if (led1Flash) { led1Flash.flashing = input === 'tak'; }
                else { addMessage('Wystąpił błąd spróbuj ponownie później'); }
                stepIndex = duo ? 5 : 6;
                break;
            case 5:
                const led2Flash = learning.actions.find(a => a.target === 'led2');
                if (led2Flash) { led2Flash.flashing = input === 'tak'; }
                else { addMessage('Wystąpił błąd spróbuj ponownie później'); }
                stepIndex = 6;
                break;
        }

        if (stepIndex < learningSteps.length) {
            addMessage(learningSteps[stepIndex], 'bot');
        } else {
            commands[learning.command] = {
                message: learning.response,
                actions: learning.actions
            };

            fetch('/save-commands', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ commands })
            })
            .then(() => addMessage('Dziękuję, nauczyłem się tej komendy! 🎉', 'bot'))
            .catch(() => addMessage('Wystąpił błąd podczas zapisu komendy 😢', 'bot'));

            learning = null;
        }
    }

    // Emoji Picker toggle and logic
    emojiBtn.addEventListener('click', () => {
        emojiPicker.style.display = emojiPicker.style.display === 'none' ? 'block' : 'none';
        const rect = emojiBtn.getBoundingClientRect();
        emojiPicker.style.position = 'absolute';
        emojiPicker.style.left = `${rect.left}px`;
        emojiPicker.style.top = `${rect.top - 350}px`; // wysokość pickera
    });

    document.querySelector('emoji-picker').addEventListener('emoji-click', event => {
        chatInput.value += event.detail.unicode;
        emojiPicker.style.display = 'none';
        chatInput.focus();
    });

    document.addEventListener('click', function(e) {
        if (!emojiPicker.contains(e.target) && !emojiBtn.contains(e.target)) {
            emojiPicker.style.display = 'none';
        }
    });
    </script>
</x-app-layout>
