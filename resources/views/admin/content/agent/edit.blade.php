@extends('admin.content.layouts/layoutMaster')

@section('content')
 <livewire:admin.agent.edit-agent :agentId="$agentId" />
@endsection