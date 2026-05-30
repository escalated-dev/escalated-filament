<?php

namespace Escalated\Filament\Support;

use Escalated\Laravel\Contracts\TicketSubject;
use Escalated\Laravel\Models\Ticket;
use Escalated\Laravel\Models\TicketSubjectLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\ValidationException;

/**
 * Resolves ticket subject types strictly against {@see config('escalated.ticket_subjects.types')}.
 */
class TicketSubjectTypeResolver
{
    public static function isAvailable(): bool
    {
        return class_exists(TicketSubjectLink::class) && method_exists(Ticket::class, 'attachSubject');
    }

    public static function isConfigured(): bool
    {
        return static::isAvailable() && static::allowedTypes() !== [];
    }

    /**
     * @return list<string>
     */
    public static function allowedTypes(): array
    {
        return collect((array) config('escalated.ticket_subjects.types', []))
            ->flatMap(fn ($value, $key) => is_string($key) ? [$key, $value] : [$value])
            ->filter(fn ($type) => is_string($type) && $type !== '')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public static function typeOptions(): array
    {
        $options = [];

        foreach (static::allowedTypes() as $type) {
            $options[$type] = static::labelForType($type);
        }

        return $options;
    }

    public static function labelForType(string $type): string
    {
        $class = Relation::getMorphedModel($type) ?? $type;

        if (is_string($class) && class_exists($class)) {
            return class_basename($class);
        }

        return $type;
    }

    public static function resolveModelClass(string $type): string
    {
        if (! in_array($type, static::allowedTypes(), true)) {
            throw ValidationException::withMessages([
                'type' => "Subject type [{$type}] is not an allowed ticket subject.",
            ]);
        }

        $class = Relation::getMorphedModel($type) ?? $type;

        if (! is_string($class) || ! class_exists($class) || ! is_subclass_of($class, Model::class)) {
            throw ValidationException::withMessages([
                'type' => "Subject type [{$type}] could not be resolved to a model.",
            ]);
        }

        return $class;
    }

    /**
     * @return array<string, string>
     */
    public static function subjectOptionsForType(?string $type): array
    {
        if ($type === null || $type === '') {
            return [];
        }

        $class = static::resolveModelClass($type);

        return $class::query()
            ->limit(100)
            ->get()
            ->mapWithKeys(function (Model $model): array {
                $label = $model instanceof TicketSubject
                    ? $model->ticketSubjectTitle()
                    : (string) ($model->getAttribute('name') ?? $model->getKey());

                return [(string) $model->getKey() => $label];
            })
            ->all();
    }
}
