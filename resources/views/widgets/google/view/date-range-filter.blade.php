<form id="date-range-form" class="space-y-4 p-4 bg-white shadow rounded-lg dark:bg-gray-900 dark:shadow-md">
    <div class="flex flex-col">
        <label for="start_date" class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Start Date:</label>
        <input type="date" id="start_date" wire:model="startDate"
            class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:focus:ring-blue-400">
    </div>
    <div class="flex flex-col">
        <label for="end_date" class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">End Date:</label>
        <input type="date" id="end_date" wire:model="endDate"
            class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:focus:ring-blue-400">
    </div>
    <button type="button" onclick="submitDateRangeForm()"
        style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);"
        class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action">
        Submit
    </button>
</form>

<script>
    function submitDateRangeForm() {
        const startDate = document.querySelector('input[id="start_date"]').value;
        const endDate = document.querySelector('input[id="end_date"]').value;
        const url = new URL(window.location.href);
        url.searchParams.set('start_date', startDate);
        url.searchParams.set('end_date', endDate);
        window.location.href = url.toString();
    }
</script>