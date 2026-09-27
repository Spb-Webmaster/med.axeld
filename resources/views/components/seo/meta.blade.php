@props([
    'title'       => '',
    'description' => '',
    'keywords'    => '',
])
@if(isset($seo_title))
     @php
         $title = $seo_title;
     @endphp
@endif
@if(isset($seo_description))
    @php
        $description = $seo_description;
    @endphp
@endif
@if(isset($seo_keywords))
    @php
        $title = $seo_title;
    @endphp
@endif
@section('title', ($title)?:null)
@section('description', ($description)?:null)
@section('keywords', ($keywords)?:null)
