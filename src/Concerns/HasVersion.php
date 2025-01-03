<?php

namespace PcbPlus\PcbCpq\Concerns;

use PcbPlus\PcbCpq\Exceptions\RuntimeException;
use PcbPlus\PcbCpq\Models\Version;

trait HasVersion
{
    /**
     * @param int $versionId
     * @return \PcbPlus\PcbCpq\Models\Version
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function findVersionOrAbort($versionId)
    {
        $version = Version::find($versionId);

        if (! $version) {
            throw new RuntimeException('Version not found');
        }

        return $version;
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Version $version
     * @return void
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function validateVersionIsActivable($version)
    {
        $this->validateVersionIsSubmitted($version);

        $this->validateVersionIsInactive($version);
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Version $version
     * @return void
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function validateVersionIsDeletable($version)
    {
        return $this->validateVersionIsInactive($version);
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Version $version
     * @return void
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function validateVersionIsEditable($version)
    {
        $this->validateVersionIsUnsubmitted($version);

        $this->validateVersionIsInactive($version);
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Version $version
     * @return void
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function validateVersionIsSubmittable($version)
    {
        $this->validateVersionIsUnsubmitted($version);

        $this->validateVersionIsInactive($version);
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Version $version
     * @return void
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function validateVersionIsUnsubmittable($version)
    {
        $this->validateVersionIsSubmitted($version);

        $this->validateVersionIsInactive($version);
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Version $version
     * @return void
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function validateVersionIsActive($version)
    {
        if (! $version->is_active) {
            throw new RuntimeException('Version must be active');
        }
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Version $version
     * @return void
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function validateVersionIsInactive($version)
    {
        if ($version->is_active) {
            throw new RuntimeException('Version must be inactive');
        }
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Version $version
     * @return void
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function validateVersionIsSubmitted($version)
    {
        if (! $version->is_submitted) {
            throw new RuntimeException('Version must be submitted');
        }
    }

    /**
     * @param \PcbPlus\PcbCpq\Models\Version $version
     * @return void
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function validateVersionIsUnsubmitted($version)
    {
        if ($version->is_submitted) {
            throw new RuntimeException('Version must be unsubmitted');
        }
    }
}
