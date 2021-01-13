<?php

namespace Charcoal\Cms\Support\Helpers;

use DateTime;
use Exception;

// From 'charcoal-translator'
use Charcoal\Translator\TranslatorAwareTrait;

/**
 * Class DateHelper
 */
class DateHelper
{
    use TranslatorAwareTrait;

    /**
     * @var DateTime $from
     */
    protected $from;

    /**
     * @var DateTime $to
     */
    protected $to;

    /**
     * @var array $dateFormats The date formats options from config.
     */
    protected $dateFormats;

    /**
     * @var array $timeFormats The time formats options from config.
     */
    protected $timeFormats;

    /**
     * @var string $dateFormat The format from dateFormats to use for the date
     */
    protected $dateFormat;

    /**
     * @var string $dateFormat The format from dateFormats to use for the time
     */
    protected $timeFormat;

    /**
     * DateHelper constructor.
     * @param array $data DateHelper data.
     * @throws Exception When constructor's data missing.
     */
    public function __construct(array $data)
    {
        if (!isset($data['date_formats'])) {
            throw new Exception('date formats configuration must be defined in the DateHelper constructor.');
        }
        if (!isset($data['time_formats'])) {
            throw new Exception('time formats configuration must be defined in the DateHelper constructor.');
        }
        if (!isset($data['translator'])) {
            throw new Exception('Translator needs to be defined in the dateHelper class.');
        }

        $this->setTranslator($data['translator']);
        $this->dateFormats = $data['date_formats'];
        $this->timeFormats = $data['time_formats'];
    }

    /**
     * @param mixed  $date   The date
     *                       [startDate, endDate]
     *                       DateTimeInterface
     *                       string.
     * @param string $format The format to use.
     * @return string
     */
    public function formatDate($date, $format = 'default')
    {
        $this->dateFormat = $format;

        if (is_array($date)) {
            $this->from = $this->parseAsDate($date[0]);
            $this->to = !!($date[1]) ? $this->parseAsDate($date[1]) : null;
        } else {
            $this->from = $this->parseAsDate($date);
            $this->to = null;
        }

        return (string)$this->formatDateFromCase($this->getDateCase());
    }

    /**
     * @param mixed  $date   The date
     *                       [startDate, endDate]
     *                       DateTimeInterface
     *                       string.
     * @param string $format The format to use.
     * @return string
     */
    public function formatTime($date, $format = 'default')
    {
        $this->timeFormat = $format;

        if (is_array($date)) {
            $this->from = $this->parseAsDate($date[0]);
            $this->to = $this->parseAsDate($date[1]);
        } else {
            $this->from = $this->parseAsDate($date);
            $this->to = null;
        }

        return $this->formatTimeFromCase($this->getTimeCase());
    }

    /**
     * Get the usage case by comparing two dates.
     * @return string
     */
    private function getDateCase()
    {
        $from = $this->from;
        $to = $this->to;

        // single date event
        if (!$to || $to->format('Ymd') === $from->format('Ymd')) {
            return 'single';
        }

        $fromDate = [
            'day'   => $from->format('d'),
            'month' => $from->format('m'),
            'year'  => $from->format('y')
        ];

        $toDate = [
            'day'   => $to->format('d'),
            'month' => $to->format('m'),
            'year'  => $to->format('y')
        ];

        $case = null;
        $case = $fromDate['day'] !== $toDate['day'] ? 'different_day' : $case;
        $case = $fromDate['month'] !== $toDate['month'] ? 'different_month' : $case;
        $case = $fromDate['year'] !== $toDate['year'] ? 'different_year' : $case;

        return $case;
    }

    /**
     * Get the usage case by comparing two hours.
     * @return string
     */
    private function getTimeCase()
    {
        $from = $this->from;
        $to = $this->to;

        // Single hour event
        if (!$to || $to->format('Hi') === $from->format('Hi')) {
            if ($to->format('i') == 0) {
                return 'single_round';
            }

            return 'single';
        }

        $fromTime = [
            'hour'   => $from->format('H'),
            'minute' => $from->format('i'),
        ];

        $toTime = [
            'hour'   => $to->format('H'),
            'minute' => $to->format('i'),
        ];

        $case = null;
        $case = $fromTime['hour'] !== $toTime['hour'] ? 'different_time' : $case;
        $case = $fromTime['minute'] == 0 ? 'different_time_round' : $case;
        $case = $fromTime['minute'] != $toTime['minute'] ? 'different_time' : $case;

        return $case;
    }

    /**
     * @param string $case The use case.
     * @return string
     */
    private function formatDateFromCase($case)
    {
        $dateFormats = $this->dateFormats;
        $case = $dateFormats[$this->dateFormat][$case];

        $content = $this->translator()->translation($case['content']);

        $formats['from'] = $this->translator()->translation($case['formats']['from']);
        $formats['to']   = isset($case['formats']['to'])
                           ? $this->translator()->translation($case['formats']['to'])
                           : null;

        $formats['from'] = $this->crossPlatformFormat((string)$formats['from']);
        $formats['to']   = $this->crossPlatformFormat((string)$formats['to']);

        if (!$this->to || !$formats['to']) {
            return sprintf(
                (string)$content,
                strftime($formats['from'], $this->from->getTimestamp())
            );
        }

        return sprintf(
            (string)$content,
            strftime($formats['from'], $this->from->getTimestamp()),
            strftime($formats['to'], $this->to->getTimestamp())
        );
    }

    /**
     * @param string $case The use case.
     * @return string
     */
    private function formatTimeFromCase($case)
    {
        $timeFormats = $this->timeFormats;
        $case = $timeFormats[$this->timeFormat][$case];

        $content = $this->translator()->translation($case['content']);

        $formats['from'] = $case['formats']['from'];
        $formats['to'] = isset($case['formats']['to']) ? $case['formats']['to'] : null;

        $formats['from'] = $this->translator()->translation($formats['from']);
        $formats['to'] = $this->translator()->translation($formats['to']);

        if (!$this->to || !$formats['to']) {
            return sprintf(
                (string)$content,
                strftime($formats['from'], $this->from->getTimestamp())
            );
        }

        return sprintf(
            (string)$content,
            strftime($formats['from'], $this->from->getTimestamp()),
            strftime($formats['to'], $this->to->getTimestamp())
        );
    }

    /**
     * @param mixed $date The date to convert.
     * @return DateTime
     */
    private function parseAsDate($date)
    {
        if ($date instanceof \DateTimeInterface) {
            return $date;
        }

        return new DateTime($date);
    }

    /**
     * @param mixed $format DateTime to be formatted.
     * @return mixed
     */
    private function crossPlatformFormat($format)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            $format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
        }

        return $format;
    }
}
