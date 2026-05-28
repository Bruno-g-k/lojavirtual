<x-app-layout>
    <div class="w-full bg-white dark:bg-gray-800 p-6
rounded-lg shadow mx-auto">

        <div class="flex justify-between items-center mb-6">

            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Produtos</h1>

            <div class="flex gap-2">
                <a href="{{ route('products.report') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                    Relatorio
                </a>
                <a href="{{ url('products/new') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Cadastrar</a>
            </div>

        </div>

        @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <table class="w-full table-auto border-collapse border border-gray-300 dark:border-gray-600">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-700">
                    <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300
                border border-gray-300 dark:border-gray-600">Imagem</th>
                    <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600">Nome</th>
                    <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600">Preço</th>
                    <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600">Quantidade</th>
                    <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600">Tipo</th>
                    <th class="px-4 py-2 text-left text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr class="border-b border-gray-300 dark:border-gray-600">

                    <td class="px-4 py-2 text-gray-900 dark:text-white">
                        @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="Imagem do
produto" class="w-20 h-20 object-cover rounded">
                        @else
                        <img src="{{ asset('storage/no-image.jpg') }}" alt="Imagem do
produto" class="w-20 h-20 object-cover rounded">
                        @endif
                    </td>

                    <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $product->name }}</td>
                    <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $product->price }}</td>
                    <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $product->quantity }}</td>
                    <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $product->type->name }}</td>
                    <td class="px-4 py-2">
                        <div class="flex justify-center space-x-2">
                            <a href="{{ url('/products/update', ['id' => $product->id]) }}" class="bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700">Editar</a>
                            <a href="{{ url('/products/delete', ['id' => $product->id]) }}" class="delete-confirm bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700" data-delete-url="{{ url('/products/delete', ['id' => $product->id]) }}">Excluir</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>