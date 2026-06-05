@extends('layouts.app')

@section('content')

<div class="bg-gray-200 min-h-screen pb-3">

    @include('layouts.panelHeading')
    @include('layouts.headingContent')

    <div class="bg-white shadow rounded-lg p-4 m-4">
        <div class="logistics_body">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-5 shadow-lg">
                <div class="flex flex-col lg:flex-row gap-4 items-start">
        
                    <div class="relative flex-1 w-full">
                        <input
                            type="text"
                            id="city_from"
                            name="city_from"
                            placeholder="City From"
                            autocomplete="off"
                            class="w-full h-12 pl-4 pr-4 bg-white/95 border border-white/20 rounded-xl text-gray-700 placeholder-gray-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-white focus:shadow-lg transition-all duration-300 cursor-pointer"
                        >
        
                        <div
                            id="city_from_dropdown"
                            class="hidden absolute top-full left-0 mt-2 w-full bg-white rounded-xl shadow-xl border border-gray-100 max-h-64 overflow-y-auto z-50"
                        >
                        </div>
                    </div>
        
                    <div class="hidden lg:flex items-center justify-center pt-1">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-full flex items-center justify-center text-white">
                            ⇄
                        </div>
                    </div>
        
                    <div class="relative flex-1 w-full">
                        <input
                            type="text"
                            id="city_to"
                            name="city_to"
                            placeholder="City To"
                            autocomplete="off"
                            class="w-full h-12 pl-4 pr-4 bg-white/95 border border-white/20 rounded-xl text-gray-700 placeholder-gray-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-white focus:shadow-lg transition-all duration-300 cursor-pointer"
                        >
        
                        <div
                            id="city_to_dropdown"
                            class="hidden absolute top-full left-0 mt-2 w-full bg-white rounded-xl shadow-xl border border-gray-100 max-h-64 overflow-y-auto z-50"
                        >
                        </div>
                    </div>
        
                    <button
                        type="button"
                        id="search_route"
                        class="w-full lg:w-auto h-12 px-8 bg-white text-blue-600 font-semibold rounded-xl shadow-md hover:shadow-xl hover:-translate-y-0.5 hover:bg-gray-50 transition-all duration-300 cursor-pointer"
                    >
                        Search
                    </button>
        
                </div>
            </div>
        </div>


        <div id="route_result" class="m-4">
        </div>

        <div
            id="route_map"
            class="w-full h-[500px] mt-4 rounded-xl shadow-lg overflow-hidden"
        >
        </div>
    </div>

    <div class="pagination-container mt-4 flex justify-center">
        <ul class="pagination flex gap-2"></ul>
    </div>

</div>

@endsection



@push('scripts')
    @vite('resources/js/logistics/logistics.js')
@endpush