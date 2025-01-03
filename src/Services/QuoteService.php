<?php

namespace PcbPlus\PcbCpq\Services;

use Illuminate\Support\Collection;
use PcbPlus\PcbCpq\Exceptions\RuntimeException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class QuoteService
{
    /**
     * @param \PcbPlus\PcbCpq\Models\Product $product
     * @param array $params
     * @return \PcbPlus\PcbCpq\Models\Product
     */
    public function quoteProduct($product, $params)
    {
        $product->quote_costs = $this->quoteCosts($product, $params);

        $product->quote_leadtime = $this->quoteLeadtime($product, $params);

        return $product;
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Product $product
     * @param array $params
     * @return \Illuminate\Support\Collection
     */
    protected function quoteCosts($product, $params)
    {
        return Collection::make($product->costs)
            ->map(function ($cost) use ($params) {
                return $this->quoteCost($cost, $params);
            })
            ->filter(function ($cost) {
                return ! empty($cost->quote_rule);
            });
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Cost $cost
     * @param array $params
     * @return \PcbPlus\PcbCpq\Models\Cost
     */
    protected function quoteCost($cost, $params)
    {
        $quoteRule = $this->quoteRule($cost, $params);
        $quoteTier = $this->quoteTier($quoteRule, $params);
        $quotePrice = $this->quotePrice($quoteRule, $quoteTier, $params);

        $cost->quote_rule = $quoteRule;
        $cost->quote_tier = $quoteTier;
        $cost->quote_price = $quotePrice;

        return $cost;
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Cost $cost
     * @param array $params
     * @return \PcbPlus\PcbCpq\Models\Rule|null
     */
    protected function quoteRule($cost, $params)
    {
        return Collection::make($cost->rules)->first(function ($rule) use ($params) {
            return ! $rule->is_conditional || (bool) $this->evaluateExpression($rule->condition_expression, $params);
        });
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Rule|null $rule
     * @param array $params
     * @return \PcbPlus\PcbCpq\Models\Tier|null
     */
    protected function quoteTier($rule, $params)
    {
        if (! $rule || ! $rule->is_tiered) {
            return null;
        }

        return Collection::make($rule->tiers)->first(function ($tier) use ($params) {
            return (bool) $this->evaluateExpression($tier->condition_expression, $params);
        });
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Rule $rule
     * @param \PcbPlus\PcbCpq\Models\Tier|null $tier
     * @param array $params
     * @return float
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    protected function quotePrice($rule, $tier, $params)
    {
        $price = ! empty($tier) ? $tier->price : $rule->price;

        $multiplier = $this->quoteMultiplier($rule, $params);

        return $price * $multiplier;
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Rule $rule
     * @param array $params
     * @return float
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    protected function quoteMultiplier($rule, $params)
    {
        return (float) ($rule->is_unit ? $this->evaluateExpression($rule->multiplier_expression, $params) : 1);
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Product $product
     * @param array $params
     * @return \PcbPlus\PcbCpq\Models\Leadtime|null
     */
    protected function quoteLeadtime($product, $params)
    {
        return Collection::make($product->leadtimes)->first(function ($leadtime) use ($params) {
            return ! $leadtime->is_conditional || (bool) $this->evaluateExpression($leadtime->condition_expression, $params);
        });
    }

    /**
     * @param string $expression
     * @param array $params
     * @return mixed
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    protected function evaluateExpression($expression, $params)
    {
        try {
            return (new ExpressionLanguage())->evaluate($expression, $params);
        } catch (\Exception $e) {
            throw new RuntimeException('Expression syntax error: ' . $expression);
        }
    }
}
