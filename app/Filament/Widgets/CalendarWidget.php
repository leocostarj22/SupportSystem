<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Filament\Resources\EventResource;
use Filament\Actions;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Data\EventData;

class CalendarWidget extends FullCalendarWidget
{
    public function createEvent(array $data): void
    {
        $data['user_id'] = auth()->id();
        Event::create($data);
    }

    public function updateEvent(array $data): void
    {
        $event = Event::find($data['id']);
        $event->update($data);
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return Event::query()
            ->when(auth()->user()->role?->name !== 'Super Admin', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->where('start', '>=', $fetchInfo['start'])
            ->where('end', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (Event $event) => EventData::make()
                    ->id($event->id)
                    ->title($event->title)
                    ->start($event->start)
                    ->end($event->end)
                    ->allDay($event->all_day)
                    ->backgroundColor($event->color)
                    ->borderColor($event->color)
                    ->extendedProps([
                        'description' => $event->description
                    ])
                    ->url(
                        url: EventResource::getUrl('edit', ['record' => $event]),
                        shouldOpenUrlInNewTab: false
                    )
            )
            ->toArray();
            
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->label('Título')
                ->required(),
            Textarea::make('description')
                ->label('Descrição'),
            DateTimePicker::make('start')
                ->label('Início')
                ->required(),
            DateTimePicker::make('end')
                ->label('Fim')
                ->required(),
            ColorPicker::make('color')
                ->label('Cor')
                ->required(),
            Toggle::make('all_day')
                ->label('Dia inteiro')
                ->default(false),
        ];
    }

    protected function modalActions(): array
    {
        return [
            Actions\EditAction::make()
                ->mountUsing(
                    function (Event $record, Forms\Form $form, array $arguments) {
                        $form->fill([
                            'title' => $record->title,
                            'description' => $record->description,
                            'start' => $arguments['event']['start'] ?? $record->start,
                            'end' => $arguments['event']['end'] ?? $record->end,
                            'color' => $record->color,
                            'all_day' => $record->all_day,
                        ]);
                    }
                ),
            Actions\DeleteAction::make(),
        ];
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->mountUsing(
                    function (Forms\Form $form, array $arguments) {
                        $form->fill([
                            'start' => $arguments['start'] ?? null,
                            'end' => $arguments['end'] ?? null,
                            'user_id' => auth()->id(),
                        ]);
                    }
                )
        ];
    }

    protected function getOptions(): array
    {
        return [
            'locale' => 'pt-br',
            'initialView' => 'dayGridMonth',
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'editable' => true,
            'selectable' => true,
            'dayMaxEvents' => true,
            'eventDrop' => true,
            'eventResize' => true,
            'select' => true,
            'eventDidMount' => 'function(info) {
                if (typeof tippy !== "undefined") {
                    tippy(info.el, {
                        content: info.event.extendedProps.description || info.event.title,
                        placement: "top",
                        trigger: "mouseenter",
                        interactive: true
                    });
                }
            }',
            'eventMouseEnter' => 'function(info) {
                info.el.style.cursor = "pointer";
            }',
            'selectMirror' => true,
            'unselectAuto' => true,
            'selectOverlap' => false,
            'eventOverlap' => false,
        ];
    }

}
