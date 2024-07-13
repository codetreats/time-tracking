<?php

/**
 * PHPLogin\TrackingData
 */
namespace PHPLogin;

use \Datetime;

class TrackingData {
    public int $id;
    public string $user_id;
    public string $date;
    public string $start;
    public string $end;
    public string $description;
    public float $payment;

    public function __construct(int $id, string $user_id, string $date, string $start, string $end, string $description, float $payment) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->date = $date;
        $this->start = $start;
        $this->end = $end;
        $this->description = $description;
        $this->payment = $payment;
    }

    public function getDate() : DateTime {
        return DateTime::createFromFormat('Y-m-d', $this->date);
    }

    public function getStartDateTime(): DateTime {
        return new DateTime($this->date . ' ' . $this->start);
    }

    public function getEndDateTime(): DateTime {
        $endDateTime = new DateTime($this->date . ' ' . $this->end);
        $startDateTime = $this->getStartDateTime();

        if ($endDateTime < $startDateTime) {
            $endDateTime->modify('+1 day');
        }

        return $endDateTime;
    }

    public function workingTime(): float {
        $startDateTime = $this->getStartDateTime();
        $endDateTime = $this->getEndDateTime();
    
        $interval = $startDateTime->diff($endDateTime);
        $hours = $interval->h + ($interval->i / 60);
    
        return round($hours, 2);
    }
    
    public function workingTimeHumanReadable(): string {
        $startDateTime = $this->getStartDateTime();
        $endDateTime = $this->getEndDateTime();
    
        $interval = $startDateTime->diff($endDateTime);
        return sprintf('%02d:%02d', $interval->h, $interval->i);
    }

    public function payment(): float {
        return round($this->payment * $this->workingTime(), 2);
    }

    public function overlaps(array $trackings): bool {
        $startDateTime = $this->getStartDateTime();
        $endDateTime = $this->getEndDateTime();

        foreach ($trackings as $tracking) {
            $otherStartDateTime = $tracking->getStartDateTime();
            $otherEndDateTime = $tracking->getEndDateTime();

            if ($startDateTime < $otherEndDateTime && $endDateTime > $otherStartDateTime) {
                return true;
            }
        }

        return false;
    }

    public static function fromArray(array $data): self {
        return new self(
            $data['id'] ?? 0,
            $data['user_id'],
            $data['date'],
            $data['start'],
            $data['end'],
            $data['description'],
            (float)$data['payment']
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'date' => $this->date,
            'start' => $this->start,
            'end' => $this->end,
            'description' => $this->description,
            'payment' => $this->payment,
        ];
    }
}
?>