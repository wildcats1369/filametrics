<x-filament::page>
    <h2 class="text-2xl font-bold mb-6">Predict Data</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ID
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Name
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Email
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($data as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item['id'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item['name'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item['email'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament::page>