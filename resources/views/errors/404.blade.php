@extends('cms::layouts.error')
@section('title', Lang::has('cms::global.not_found', [], app()->getLocale()) ?  __('cms::global.not_found') : __('cms::global.not_found', [], 'ar')) 
@section('code', '404')
@section('message', Lang::has('cms::global.this_page_not_found', [], app()->getLocale()) ?  __('cms::global.this_page_not_found') : __('cms::global.this_page_not_found', [], 'ar')) 