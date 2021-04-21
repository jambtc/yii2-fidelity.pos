<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\web\NotFoundHttpException;
use app\models\Invoices;
use app\models\Pos;
use app\components\WebApp;
use app\components\Settings;

class ReceiveController extends Controller
{
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Invoices the loaded model
	 * @throws CHttpException
	 */
	public function loadInvoice($id)
	{
		$model = Invoices::findOne($id);
		if($model===null)
			throw new NotFoundHttpException(404,'The requested page does not exist.');

		return $model;
	}



	// scrive a video
    private function log($text){
       $time = "\r\n" .date('Y/m/d h:i:s a - ', time());
       echo  $time.$text;
    }

	public function actionIndex($id){
		set_time_limit(0); //imposto il time limit unlimited
		$seconds = 1;
		$events = true;

		$this->log("Start Check invoice #: $id");

		//carico l'invoice
		$invoice = $this->loadInvoice(WebApp::decrypt($id));
		$this->log("Invoice $id loaded. Status is $invoice->status");

		//$expiring_seconds = $invoice->expiration_timestamp +1 - time();
		$transactionValue = $invoice->received;
		$SEARCH_ADDRESS = strtoupper($invoice->to_address);

		$pos = Pos::find(['id' => $invoice->id_pos])->one();
		$store = $pos->store;
		$blockchain = Settings::poa($store->id_blockchain);

		// $block = Yii::$app->Erc20->getBlockInfo();
		// $blockLatest = $block->number;
		// $blockLatest = '0x4f6780';
		// echo '<pre>'.print_r($blockLatest,true);exit;

		$ERC20 = new Yii::$app->Erc20($blockchain->id);

		$x = 0;

		while(true){
			$ipnflag = false;
			//se il valore è new proseguo
			if ($invoice->status == 'new'){
				// cerca nel blocco attuale con dettagli (true)
				$block = $ERC20->getBlockInfo('latest', true);
				// $block = Yii::$app->Erc20->getBlockInfo('latest', true);

				$this->log('Block è: <pre>'.print_r($block,true).'</pre>');

				if (isset($block ))
					$transactions = $block->transactions;

				$this->log("Ricerca su block n. $block->number");
				// $this->log('Transazioni è: <pre>'.print_r($transactions,true).'</pre>');

				if (isset($transactions) && !empty($transactions))
				{
					$this->log("Transaction piena on block n. $block->number");

					foreach ($transactions as $transaction)
					{
						$this->log('Transazione singola è: <pre>'.print_r($transaction,true).'</pre>');

						//controlla transazioni ethereum
						if (strtoupper($transaction->to) <> strtoupper($blockchain->smart_contract_address) ){
							$this->log(" : è una transazione ether...\n");
							$ReceivingType = 'ether';
					    }else{
						    $this->log(" : è una transazione token...\n");
						    //smart contract
						    $ReceivingType = 'token';
						    // $transactionId = $transaction->hash;
						    // recupero la ricevuta della transazione tramite hash
							$transactionContract = $ERC20->getReceipt($transaction->hash);
							$transactionContract = Yii::$app->Erc20->getReceipt($transaction->hash);

							if ($transactionContract <> '' && !(empty($transactionContract->logs)))
 						    {
 							   $this->log(" : è una transazione token non vuota...\n");
 							   $receivingAccount = $transactionContract->logs[0]->topics[2];
 							   $receivingAccount = str_replace('000000000000000000000000','',$receivingAccount);

 							   // verifica se nella transazione RICEVI, MA NON SE HAI INVIATO
 							   if (strtoupper($receivingAccount) == $SEARCH_ADDRESS ){
 								    $this->log(" : è una transazione token che appartiene all'utente in RICEZIONE...\n");

									$transactionValue = $ERC20->wei2eth(
										$transactionContract->logs[0]->data,
										$blockchain->decimals
									); // decimali del token

									// $transactionValue = Yii::$app->Erc20->wei2eth(
									// 	$transactionContract->logs[0]->data,
									// 	$blockchain->decimals
									// ); //

									// aggiorno il database
									$invoice->received = $transactionValue;
									$invoice->from_address = $transaction->from;
									$invoice->txhash = $transactionContract->transactionHash;
									$invoice->status = 'complete';

									$ipnflag = true;
									break; //foreach
								}
							}
						}
					}//foreach loop
				}
				if ($ipnflag === false && ($invoice->expiration_timestamp-time()) < 0){//invoice expired
					$invoice->status = 'expired';
					$ipnflag = true;
				}
			} else {
				$ipnflag = true;
			}
			if (($invoice->expiration_timestamp-time()) < 0){//invoice expired
				$ipnflag = true;
			}
			if ($ipnflag){ //send ipn in case flag is true: può venire
				#fwrite($this->getLogFile(), date('Y/m/d h:i:s a', time()) . " : <pre>".print_r($tokens->attributes,true)."</pre>\n");
				//echo '<pre>'.print_r($invoice->attributes,true).'</pre>';
				if ($invoice->save()){
					$this->log("Invoice n. $invoice->id SALVATA.");
				}else{
					$this->log("Error : Cannot save invoice #. $id, Status: $invoice->status.");
				}

				break;
			}

			//conto alla rovescia fino alla scadenza dell'invoice
			$this->log("Invoice: $id, Status: ".$invoice->status.", Seconds: ".($invoice->expiration_timestamp-time())."\n");
			$this->log("remaining seconds...". ($invoice->expiration_timestamp-time()) );
			// $expiring_seconds --;
			// $x++;
			//sleep(1);


		// testing
			// if ($searchBlock == '0x4f6d84')
			// 	break;
		}
	}


}
?>
