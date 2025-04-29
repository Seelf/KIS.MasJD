<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Devices') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Here you can view devices") }}
                </div>

                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(isset($response))
                        <?php var_dump($response) ?>
                    @endif
                </div>

                <form class="p-4 text-gray-900 dark:text-gray-100" action="{{ route('set.led') }}" method="POST">
                    @csrf
                    <label>URN urządzenia:</label>
                    <input type="text" name="device_urn" placeholder="urn:rafi:sbox:9c65f93cbf19" required>

                    <label>Kolor:</label>
                    <input type="text" name="color" placeholder="red" required>

                    <label>Miganie:</label>
                    <select name="flashing">
                        <option value="false">Nie</option>
                        <option value="true">Tak</option>
                    </select>

                    <label>Cel:</label>
                    <input type="text" name="target" placeholder="led1" required>

                    <button type="submit">Ustaw LED</button>
                </form>

                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700">
                            <th class="border border-gray-300 p-2">ID</th>
                            <th class="border border-gray-300 p-2">URN</th>
                            <th class="border border-gray-300 p-2">Name</th>
                            <th class="border border-gray-300 p-2">Asset Groups</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($devices as $device): ?>
                        <tr class="border border-gray-300">
                            <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($device['id']); ?></td>
                            <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($device['urn']); ?></td>
                            <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($device['name']); ?></td>
                            <td class="border border-gray-300 p-2">
                                    <?php echo implode(', ', array_map('htmlspecialchars', $device['assetGroupIDs'])); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
