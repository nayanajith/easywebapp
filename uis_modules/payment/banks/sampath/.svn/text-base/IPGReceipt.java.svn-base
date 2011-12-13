/**
 * @author nml@ucsc.lk
 */
import org.apache.commons.cli.*;
import iClient.CShroff;

/* 
 * Stub program that reads command line arguments
 */
public class IPGReceipt {

	/*Command line options*/
	private static Options options = null; 

	/*Requesting command line parameters*/
	private static final String RECEIPT		="r";
	private static final String KEY_PATH	="k";

	/*To hold input values*/
	private String key_path;
	private String receipt;

	/*Command Line arguments*/
	private CommandLine cmd = null; 
	
	/*Option list*/
	static{
		options = new Options();
		options.addOption(RECEIPT		,true,"Recipt string from the bank");
		options.addOption(KEY_PATH		,true,"Path to ipg keys");
	}
	

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		IPGReceipt cliProg = new IPGReceipt();
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
		if(!cmd.hasOption(RECEIPT) || !cmd.hasOption(KEY_PATH)){
			HelpFormatter formatter = new HelpFormatter();
			formatter.printHelp("java -cp lib/commons-cli-1.2.jar:lib/iclient.jar:lib/ibmjceprovider.jar:lib/ibmpkcs.jar:. IPGReceipt <args>", options);
			System.exit(1);
		}else{
			receipt		=cmd.getOptionValue(RECEIPT);
			key_path		=cmd.getOptionValue(KEY_PATH);

			/*Print invoice string*/
			System.out.println(getIPGRecipt(receipt, key_path));
		}
		
		/*Look for optional args.*/ 
		/*
		if (cmd.hasOption(AMOUNT)){
			outputFile = cmd.getOptionValue(AMOUNT);
		}
		*/
	}

  	/**
	 * @param receipt, 
	 * @param key_path, 
	 */
   private String getIPGRecipt(String receipt,String key_path) {
      
      CShroff theClientShroff = new CShroff(key_path);
      
      theClientShroff.setReceipt(receipt);
      String transactionID = theClientShroff.getReceivedTransactionID();
      String transactionStatus = theClientShroff.getReceivedTransactionStatus();
		return transactionID+":"+transactionStatus;
   }
}
