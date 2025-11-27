@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">{{ __('messages.help') }}</h1>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="prose prose-lg max-w-none">
                <h2 class="text-2xl font-semibold mb-4">{{ __('help.faq_title') }}</h2>
                
                <div class="space-y-6">
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold mb-2">{{ __('help.enroll_question') }}</h3>
                        <p class="text-gray-700">{{ __('help.enroll_answer') }}</p>
                    </div>
                    
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold mb-2">{{ __('help.progress_question') }}</h3>
                        <p class="text-gray-700">{{ __('help.progress_answer') }}</p>
                    </div>
                    
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold mb-2">{{ __('help.assignment_question') }}</h3>
                        <p class="text-gray-700">{{ __('help.assignment_answer') }}</p>
                    </div>
                    
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold mb-2">{{ __('help.refund_question') }}</h3>
                        <p class="text-gray-700">{{ __('help.refund_answer') }}</p>
                    </div>
                    
                    <div class="pb-6">
                        <h3 class="text-lg font-semibold mb-2">{{ __('help.achievement_question') }}</h3>
                        <p class="text-gray-700">{{ __('help.achievement_answer') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                <p class="text-gray-700">
                    <strong>{{ __('help.need_more_help') }}</strong> {{ __('help.contact_support') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
