<?php

namespace VWM\Apps\WorkOrder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class UpdatePfpLibraryCommand extends Command
{

    protected function configure()
    {
        $this
                ->setName('UpdatePfpLibrary')
                ->setDescription('Update or Edit Pfp Library from file')
                ->addArgument(
                        'file', InputArgument::OPTIONAL, 'path to file'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        if ($file == "") {
            $output->writeln('set file as argument please!');
            return false;
        }
        $logger = \VOCApp::getInstance()->getService('mixLogger');
        //get pfp names
        $db = \VOCApp::getInstance()->getService('db');
        $user = \VOCApp::getInstance()->getUser();
        $mapper = new \VWM\Import\Pfp\PfpUploaderMapper();
        $mapper->doMapping($file);
        $isMWS = false;
        $csvHelper = new \VWM\Import\CsvHelper();
        $csvHelper->openCsvFile($file);
        //get file content
        $fileData = $csvHelper->getFileContent();
        //get pfp names
        $pfpNames = array();
        $isProprietary = 0;
        //get pfp names
        foreach ($fileData as $dat) {
            //empty string
            if ($dat[$mapper->mappedData['number']] == '' && $dat[$mapper->mappedData['productId']] == '' && $dat[$mapper->mappedData['productName']] == '' && $dat[$mapper->mappedData['ratio']] == '' && $dat[$mapper->mappedData['unitType']] == '' && $dat[$mapper->mappedData['IP']] == '') {
                continue;
            }
            // get name of pfp if we need
            if ($dat[$mapper->mappedData['number']] != '') {
                //add pfp to pfpName
                if (!is_null($currentPfpName)) {
                    $pfpNames[] = $currentPfpName;
                }
                $currentPfpName = '';
                $isProprietary = 0;
                if ($dat[$mapper->mappedData['IP']] == 'IP') {
                    $isProprietary = 1;
                    $currentPfpName = $dat[$mapper->mappedData['productName']];
                } else {
                    $currentPfpName = "/ " . $dat[$mapper->mappedData['productName']];
                }
            }
            //get all pfp product description if we need
            if (!$isProprietary) {
                $currentPfpName.='/ ' . $dat[$mapper->mappedData['productName']];
            }
        }

        //add last pfpName;
        $pfpNames[] = $currentPfpName;
        // get pfp ids
        $pfpNames = implode("','", $pfpNames);
        $pfpNames = "'" . $pfpNames . "'";
        $query = "SELECT id FROM " . \VWM\Apps\WorkOrder\Entity\Pfp::TABLE_NAME . " " .
                "WHERE description IN ($pfpNames)";
        $db->query($query);
        $results = $db->fetch_all_array();
        $pfpIds = array();
        foreach ($results as $result) {
            $pfpIds[] = $result['id'];
        }
        $pfpIds = implode(',', $pfpIds);
        //get all mixes
        $query = "SELECT * FROM " . \Mix::TABLE_NAME . " " .
                "WHERE pfp_id IN ({$db->sqltext($pfpIds)})";
        $db->query($query);
        $results = $db->fetch_all_array();

        //build Mix
        $outputMixIds = array();
        foreach ($results as $result) {
            $mix = new \MixOptimized($db, $result['mix_id']);
            //get Mix Product by pfp product
            $pfp = $mix->pfp;
            //get pfp product from DB
            $pfpProducts = $mix->pfp->products;
            // aray of mix product
            $mixProducts = array();
            //get first mix product data for counting quantity (first product must be primary one)
            $query = "SELECT * " .
                    "FROM " . TB_MIXGROUP . " " .
                    "WHERE mix_id={$db->sqltext($result['mix_id'])} LIMIT 1";
            $db->query($query);
            $productData = $db->fetch_all();
            
            // get unit type by primary product
            $selectUnittype = $productData[0]->unit_type;

            $primaryProduct = $pfpProducts[0];

            $productId = $pfpProducts[0]->product_id;
            //get devisor
            if ($primaryProduct->ratio > 0) {
                $divisor = $primaryProduct->ratio;
            } else {
                $divisor = 1;
            }

            $quantity = $productData[0]->quantity;
            //array of products
            $productsDetails = array();
            $products = array();
            //COUNT PRODUCT QUANTITY BY NEW PFP
            foreach ($pfpProducts as $pfpProduct) {
                if ($pfpProduct->isRange) {
                    $pr_ratio = $pfpProduct->ratio * $primaryProduct->ratio / 100;
                } else {
                    $pr_ratio = $pfpProduct->ratio;
                }
                $q_tmp = ($pr_ratio / $divisor) * $quantity;
                $pr_id = $pfpProduct->product_id;
                $q_tmp = round($q_tmp, 2);

                $product = new \MixProduct($db);
                $product->quantity = $q_tmp;
                $product->initializeByID($pfpProduct->product_id);

                $unittype = new \Unittype($db);
                $unittypeDetails = $unittype->getUnittypeDetails($selectUnittype);

                $product->unit_type = $unittypeDetails['name'];
                $product->unittypeDetails = $unittypeDetails;

                $product->json = json_encode($product);

                $product->is_primary = ($pfpProduct->isPrimary) ? 1 : 0;
                $product->ratio_to_save = (isset($pfpProduct->ratio)) ? $pfpProduct->ratio : null;
                $products[] = $product;
            }
            $mix->products = $products;
            $mix->getFacility();
            $mix->calculateCurrentUsage();
            $id = $mix->save();
            if($id){
                $logger->addNotice('mix with id '.$id.' was updated sucessfull');
                $outputMixIds[] = $id;
            }else{
                $logger->addError('error in mix updated. Mix Id: '.$result['mix_id']);
            }
        }

        $outputMixIds = implode(',', $outputMixIds);
        $output->writeln($outputMixIds);
    }

}
?>
