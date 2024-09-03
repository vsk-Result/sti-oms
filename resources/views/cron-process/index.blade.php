@extends('layouts.app')

@section('title', 'Статус фоновых процессов')
@section('toolbar-title', 'Статус фоновых процессов')
@section('breadcrumbs', Breadcrumbs::render('cron_processes.index'))

@section('content')
    <div class="post">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-5 mb-xl-8">
                    <div class="card-body py-3">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-125px">Команда</th>
                                <th class="min-w-125px">Название</th>
                                <th class="min-w-125px">Период выполнения</th>
                                <th class="min-w-125px">Дата последнего выполнения</th>
                                <th class="min-w-125px">Статус</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold fs-6">
                                @foreach($processes as $process)
                                    <tr>
                                        <td class="fw-bolder">{{ $process->command }}</td>
                                        <td>
                                            {{ $process->title }}

                                            @if (!empty($process->description))
                                                <p>{{ $process->description }}</p>
                                            @endif
                                        </td>
                                        <td>{{ $process->period }}</td>
                                        <td>{{ $process->getLastExecutedDate() }}</td>
                                        <td>
                                            @include('partials.status', ['status' => $process->getStatus()])

                                            @if ($process->status_id === \App\Models\Status::STATUS_BLOCKED)
                                                <p class="text-danger fs-7 mt-2">{{ $process->last_error }}</p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
