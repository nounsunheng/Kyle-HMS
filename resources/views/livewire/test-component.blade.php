<div class="p-6 max-w-sm mx-auto bg-white rounded-xl shadow-lg mt-10">
    <h2 class="text-2xl font-bold mb-4">Livewire Test Component</h2>

    <div class="text-center">
        <p class="text-3xl font-bold text-blue-600 mb-4">{{ $count }}</p>

        <div class="space-x-2">
            <button wire:click="increment"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Increment
            </button>

            <button wire:click="decrement"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                Decrement
            </button>
        </div>
    </div>
</div>
