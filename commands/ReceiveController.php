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
use app\components\ApiLog;

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
    private function log($text, $die=false){
        $log = new ApiLog;
        $time = "\r\n" .date('Y/m/d h:i:s a - ', time());
        echo  $time.$text;
        $log->save('pos.command','receive','index', $time.$text, $die);
    }


	public function actionIndex($id){
		set_time_limit(0); //imposto il time limit unlimited
		$events = true;

		$this->log("Start Check invoice #: $id");

		//carico l'invoice
		$invoice = $this->loadInvoice(WebApp::decrypt($id));
		$this->log("Invoice $id loaded. Status is $invoice->status");

		$transactionValue = $invoice->received;
		// $SEARCH_ADDRESS = strtoupper($invoice->to_address);

		$pos = Pos::find(['id' => $invoice->id_pos])->one();
		$store = $pos->store;
		$blockchain = Settings::poa($store->id_blockchain);

		$ERC20 = new Yii::$app->Erc20($blockchain->id);

		$x = 0;
		while(true){
			$ipnflag = false;
			//se il valore è new proseguo
			if ($invoice->status == 'new'){
				// cerca nel blocco attuale con dettagli (true)
				$block = $ERC20->getBlockInfo('latest', true);
				// $this->log('Block è: <pre>'.print_r($block,true).'</pre>');

				if (isset($block)){
					$transactions = $block->transactions;
				}

				//$this->log("Ricerca su block n. $block->number & $oldBlock");
				// $this->log('Transazioni è: <pre>'.print_r($transactions,true).'</pre>');

				if (isset($transactions) && !empty($transactions))
				{
					$this->log("Transaction piena on block n. $block->number");
					foreach ($transactions as $idx => $trans)
					{
						$inputinfo = $trans->input;
						$inputinit = substr($inputinfo,0,10);

						# Check if transaction is a contract transfer
						if ($trans->value == '0x0' && $inputinit != '0xa9059cbb') {
							continue;
						}

						# Check if transaction is a contract transfer
						if ($inputinit == '0xa9059cbb') {
							if ($invoice->to_address == '0x'.substr($inputinfo,34,40) ){
								$this->log("Transazione token che appartiene all'utente in RICEZIONE...");
								$transactionValue = hexdec(substr($inputinfo,-64)) / pow(10, $blockchain->decimals);
								// aggiorno invoice
								$invoice->received = $transactionValue;
								$invoice->from_address = $trans->from;
								$invoice->txhash = $trans->hash;
								$invoice->status = 'complete';

								$ipnflag = true;
								break; //foreach
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
				if ($invoice->save()){
					$this->log("Invoice n. $id PAGATA.<br>Importo:$invoice->price<br>Pagato: $transactionValue<br>Hash: $invoice->txhash");
				}else{
					$this->log("Error : Cannot save invoice #. $id, Status: $invoice->status.");
				}

				break;
			}

			//conto alla rovescia fino alla scadenza dell'invoice
			if (isset($block)){
				$this->log("Invoice: $id<br>Amount: ".$invoice->price."<br>Block: $block->number<br>Seconds: ".($invoice->expiration_timestamp-time()));
			}

			sleep(1);
		}
	}


}
?>
