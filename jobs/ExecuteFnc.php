<?php
/**
 * Copyright (c) 2018 Viamage Limited
 * All Rights Reserved
 */

namespace Waka\Utils\Jobs;

use Waka\Wakajob\Classes\JobManager;
use Waka\Wakajob\Classes\RequestSender;
use Waka\Wakajob\Contracts\WakajobQueueJob;
use Winter\Storm\Database\Model;
use Viamage\CallbackManager\Models\Rate;
use Waka\Mailer\Classes\MailCreator;
use Waka\Utils\Classes\DataSource;

/**
 * Class SendRequestJob
 *
 * Sends POST requests with given data to multiple target urls. Example of Wakajob Job.
 *
 * @package Waka\Wakajob\Jobs
 */
class ExecuteFnc implements WakajobQueueJob
{
    /**
     * @var int
     */
    public $jobId;

    /**
     * @var JobManager
     */
    public $jobManager;

    /**
     * @var array
     */
    private $data;

    /**
     * @var bool
     */
    private $updateExisting;

    /**
     * @var int
     */
    private $chunk;

    /**
     * @var string
     */
    private $table;

    /**
     * @param int $id
     */
    public function assignJobId(int $id)
    {
        $this->jobId = $id;
    }

    /**
     * SendRequestJob constructor.
     *
     * We provide array with stuff to send with post and array of urls to which we want to send
     *
     * @param array  $data
     * @param string $model
     * @param bool   $updateExisting
     * @param int    $chunk
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->updateExisting = true;
        $this->chunk = 1;
    }

    /**
     * Job handler. This will be done in background.
     *
     * @param JobManager $jobManager
     */
    public function handle(JobManager $jobManager)
    {
        /**
         * travail preparatoire sur les donnes
         */
        $modelClass = $this->data['modelClass'];
        $fncName = $this->data['fncName'];
        $targets = $this->data['listIds'];

        $loop = 1;
        $jobManager->startJob($this->jobId, \count($targets));
        $send = 0;
        $scopeError = 0;
        $skipped = 0;
        // Fin inistialisation

        //Travail sur les données
        $targets = array_chunk($targets, $this->chunk);

        //trace_log($targets);
        
        try {
            foreach ($targets as $chunk) {
                foreach ($chunk as $targetId) {
                    // TACHE DU JOB
                    if ($jobManager->checkIfCanceled($this->jobId)) {
                        $jobManager->failJob($this->jobId);
                        break;
                    }
                    $jobManager->updateJobState($this->jobId, $loop);
                    /**
                     * DEBUT TRAITEMENT **************
                     */
                    $model = $modelClass::find($targetId);
                    $model->{$fncName}();
                    //trace_log("DEBUT TRAITEMENT **************");
                    
                    /**
                     * FIN TRAITEMENT **************
                     */
                }
                $loop += $this->chunk;
            }
            $jobManager->updateJobState($this->jobId, $loop);
            $jobManager->completeJob(
                $this->jobId,
                [
                'Message' => \count($targets).' '. 'Execution de fonctions par lot',
                'Demandé' => $send,
                'Erreur' => $scopeError,
                'Non traité' => $skipped,
                ]
            );
        } catch (\Exception $ex) {
            //trace_log("Exception");
            /**/trace_log($ex->getMessage());
            $jobManager->failJob($this->jobId, ['error' => $ex->getMessage()]);
        }
    }
}
