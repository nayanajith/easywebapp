/**
 * @author nml@ucsc.lk
 */
import org.apache.commons.cli.*;
import iClient.CShroff;

/* 
 * Stub program that reads command line arguments
 */
public class IPGInvoice {

	/*Command line options*/
	private static Options options = null; 

	/*Requesting command line parameters*/
	private static final String KEY_PATH			="kp";
	private static final String RETURN_URL			="ru";
	private static final String MERCHANT_ID		="mi";
	private static final String TRANSACTION_ID	="ti";
	private static final String AMOUNT				="am";

	/*To hold input values*/
	private String key_path;
	private String return_url;
	private String merchant_id;
	private String transaction_id;
	private String amount;

	/*Command Line arguments*/
	private CommandLine cmd = null; 
	
	/*Option list*/
	static{
		options = new Options();
		options.addOption(KEY_PATH			,true,"Key directory path");
		options.addOption(RETURN_URL		,true,"Return URL");
		options.addOption(MERCHANT_ID		,true,"Merchant id");
		options.addOption(TRANSACTION_ID	,true,"Transaction id");
		options.addOption(AMOUNT			,true,"Amount");
	}
	

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		IPGInvoice cliProg = new IPGInvoice();
		cliProg.loadArgs(args);
	}
	
	/**
	 * Validate and set command line arguments.
	 * Exit after printing usage if anything is astray
	 * @param args String[] args as featured in public static void main()
	 */
	private void loadArgs(String[] args){
		CommandLineParser parser = new PosixParser();
		try {
			cmd = parser.parse(options, args);
		} catch (ParseException e) {
			System.err.println("Error parsing arguments");
			e.printStackTrace();
			System.exit(1);
		}
		
		/*Check for mandatory args*/ 		
		if(
			!cmd.hasOption(KEY_PATH)||
			!cmd.hasOption(RETURN_URL)||
			!cmd.hasOption(MERCHANT_ID)||
			!cmd.hasOption(TRANSACTION_ID)||
			!cmd.hasOption(AMOUNT)){
			HelpFormatter formatter = new HelpFormatter();
			formatter.printHelp("java -cp lib/commons-cli-1.2.jar:lib/iclient.jar:lib/ibmjceprovider.jar:lib/ibmpkcs.jar:. IPGInvoice <args>", options);
			System.exit(1);
		}else{
			key_path			=cmd.getOptionValue(KEY_PATH);
			return_url		=cmd.getOptionValue(RETURN_URL);
			merchant_id		=cmd.getOptionValue(MERCHANT_ID);
			transaction_id	=cmd.getOptionValue(TRANSACTION_ID);
			amount			=cmd.getOptionValue(AMOUNT);

			/*Print invoice string*/
			System.out.println(getIPGInvoice(merchant_id, transaction_id, amount, key_path, return_url));
		}
		
		/*Look for optional args.*/ 
		/*
		if (cmd.hasOption(AMOUNT)){
			outputFile = cmd.getOptionValue(AMOUNT);
		}
		*/
	}

  	/**
	 * @param merchantId, 
	 * @param transactionID, 
	 * @param amount, 
	 * @param key_path, 
	 * @param return_url
	 */
   private String getIPGInvoice(String merchantId, String transactionID, String amount, String key_path, String return_url) {
   	CShroff theClientShroff = new CShroff(key_path);
   	theClientShroff.setMerchantID(merchantId);
      theClientShroff.setTransactionAmount(amount);
      theClientShroff.setTransactionID(transactionID);
      theClientShroff.setReturnURL(return_url);
      return theClientShroff.getInvoice();
   }
}
