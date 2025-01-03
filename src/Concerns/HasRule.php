<?php

namespace PcbPlus\PcbCpq\Concerns;

use PcbPlus\PcbCpq\Exceptions\RuntimeException;
use PcbPlus\PcbCpq\Models\Rule;

trait HasRule
{
    /**
     * @param int $ruleId
     * @return \PcbPlus\PcbCpq\Models\Rule
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function findRuleOrAbort($ruleId)
    {
        $rule = Rule::find($ruleId);

        if (! $rule) {
            throw new RuntimeException('Rule not found');
        }

        return $rule;
    }
}
