<?php

namespace Ometra\HeimdalSdk\Clients;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use Ometra\HeimdalSdk\Dtos\DataTransferObject;
use Ometra\HeimdalSdk\Dtos\HeimdalResponseDto;

class BatchClient extends AbstractClient
{
    public function imeiValidations(mixed $input, ?string $validationType = null): HeimdalResponseDto
    {
        if ($this->isCsvInput($input)) {
            return $this->submitCsv('batch/imei/validations', $input, ['validationType' => $validationType]);
        }

        $payload = ['records' => $this->records($input)];

        if ($validationType !== null) {
            $payload['validationType'] = $validationType;
        }

        return $this->http->post('batch/imei/validations', $payload);
    }

    public function imeiLocks(mixed $input): HeimdalResponseDto
    {
        return $this->submit('batch/imei/locks', $input);
    }

    public function imeiUnlocks(mixed $input): HeimdalResponseDto
    {
        return $this->submit('batch/imei/unlocks', $input);
    }

    public function subscriberActivations(mixed $input): HeimdalResponseDto
    {
        return $this->submit('batch/subscribers/activations', $input);
    }

    public function subscriberSuspends(mixed $input): HeimdalResponseDto
    {
        return $this->submit('batch/subscribers/suspends', $input);
    }

    public function subscriberResumes(mixed $input): HeimdalResponseDto
    {
        return $this->submit('batch/subscribers/resumes', $input);
    }

    public function subscriberDeactivates(mixed $input): HeimdalResponseDto
    {
        return $this->submit('batch/subscribers/deactivates', $input);
    }

    public function subscriberPredeactivates(mixed $input): HeimdalResponseDto
    {
        return $this->submit('batch/subscribers/predeactivates', $input);
    }

    public function subscriberReactivates(mixed $input): HeimdalResponseDto
    {
        return $this->submit('batch/subscribers/reactivates', $input);
    }

    public function subscriberChangesPrimary(mixed $input): HeimdalResponseDto
    {
        return $this->submit('batch/subscribers/changesprimary', $input);
    }

    public function subscriberChangesSim(mixed $input): HeimdalResponseDto
    {
        return $this->submit('batch/subscribers/changesSIM', $input);
    }

    public function subscriberChangesMsisdn(mixed $input): HeimdalResponseDto
    {
        return $this->submit('batch/subscribers/changesmsisdn', $input);
    }

    public function subscriberPurchasesSupplementary(mixed $input): HeimdalResponseDto
    {
        return $this->submit('batch/subscribers/purchasessupplementary', $input);
    }

    public function subscriberBarrings(mixed $input): HeimdalResponseDto
    {
        return $this->submit('batch/subscribers/barrings', $input);
    }

    public function subscriberUnbarrings(mixed $input): HeimdalResponseDto
    {
        return $this->submit('batch/subscribers/unbarrings', $input);
    }

    public function subscriberPreregistrations(mixed $input): HeimdalResponseDto
    {
        return $this->submit('batch/subscribers/preregistrations', $input);
    }

    public function landlineManagements(mixed $input): HeimdalResponseDto
    {
        return $this->submit('batch/landline/managements', $input);
    }

    private function submit(string $path, mixed $input): HeimdalResponseDto
    {
        if ($this->isCsvInput($input)) {
            return $this->submitCsv($path, $input);
        }

        return $this->http->post($path, ['records' => $this->records($input)]);
    }

    /**
     * @param array<string, scalar|null> $fields
     */
    private function submitCsv(string $path, mixed $input, array $fields = []): HeimdalResponseDto
    {
        if ($input instanceof UploadedFile) {
            $handle = fopen($input->getRealPath() ?: $input->getPathname(), 'r');
            if ($handle === false) {
                throw new InvalidArgumentException('Unable to read uploaded CSV file.');
            }

            try {
                return $this->http->postMultipart($path, 'csv', $handle, $input->getClientOriginalName(), $fields);
            } finally {
                fclose($handle);
            }
        }

        if (is_string($input)) {
            if (! is_file($input)) {
                throw new InvalidArgumentException("CSV path does not exist: {$input}");
            }

            $handle = fopen($input, 'r');
            if ($handle === false) {
                throw new InvalidArgumentException("Unable to read CSV path: {$input}");
            }

            try {
                return $this->http->postMultipart($path, 'csv', $handle, basename($input), $fields);
            } finally {
                fclose($handle);
            }
        }

        if (is_resource($input)) {
            return $this->http->postMultipart($path, 'csv', $input, 'batch.csv', $fields);
        }

        throw new InvalidArgumentException('Unsupported CSV input.');
    }

    private function isCsvInput(mixed $input): bool
    {
        return $input instanceof UploadedFile || is_string($input) || is_resource($input);
    }

    /**
     * @return array<int, mixed>
     */
    private function records(mixed $input): array
    {
        if ($input instanceof DataTransferObject) {
            return [$input->toArray()];
        }

        if (! is_array($input)) {
            return [$input];
        }

        $records = array_is_list($input) ? $input : [$input];

        return array_map(fn (mixed $record) => $this->record($record), $records);
    }

    private function record(mixed $record): mixed
    {
        if ($record instanceof DataTransferObject) {
            return $record->toArray();
        }

        if (is_object($record)) {
            return json_decode(json_encode($record), true) ?: [];
        }

        return $record;
    }
}
