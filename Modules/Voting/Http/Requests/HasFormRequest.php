<?php declare(strict_types=1);

namespace Modules\Voting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Voting\Entities\VotingForm;
use Modules\Voting\Exceptions\VotingFormNotFoundForMemberException;
use Modules\Voting\Repositories\FormRepository;

abstract class HasFormRequest extends FormRequest
{

    protected ?VotingForm $votingForm;

    public function __construct(
        FormRepository $formRepository,
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $this->votingForm = $formRepository->getForMember(current_member());
    }

    /**
     * @throws VotingFormNotFoundForMemberException
     */
    public function getVotingForm(): ?VotingForm
    {
        if (!$this->votingForm) {
            throw new VotingFormNotFoundForMemberException();
        }

        return $this->votingForm;
    }
}
