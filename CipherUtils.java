package com.scoopEnhancements;

import javax.crypto.Cipher;
import javax.crypto.spec.SecretKeySpec;

import org.apache.commons.codec.binary.Base64;

public class CipherUtils
{

    private static byte[] key = {
            0x74, 0x68, 0x69, 0x73, 0x49, 0x73, 0x41, 0x53, 0x65, 0x63, 0x72, 0x65, 0x74, 0x4b, 0x65, 0x79
    };//"thisIsASecretKey";

    public static String encrypt(String strToEncrypt)
    {
        try
        {
            Cipher cipher = Cipher.getInstance("AES/ECB/PKCS5Padding");
            final SecretKeySpec secretKey = new SecretKeySpec(key, "AES");
            cipher.init(Cipher.ENCRYPT_MODE, secretKey);
            final String encryptedString = Base64.encodeBase64String(cipher.doFinal(strToEncrypt.getBytes()));
            return encryptedString;
        }
        catch (Exception e)
        {
            System.out.println("Error while encrypting" + e);
        }
        return null;

    }

    public static String decrypt(String strToDecrypt)
    {
        try
        {
            Cipher cipher = Cipher.getInstance("AES/ECB/PKCS5PADDING");
            final SecretKeySpec secretKey = new SecretKeySpec(key, "AES");
            cipher.init(Cipher.DECRYPT_MODE, secretKey);
            final String decryptedString = new String(cipher.doFinal(Base64.decodeBase64(strToDecrypt)));
            return decryptedString;
        }
        catch (Exception e)
        {
            System.out.println("Error while decrypting" + e);
        }
        return null;
    }


    public static void main(String args[])
    {

        try
        {
        	final String strToEncrypt = "INPUT";
            final String encryptedStr = CipherUtils.encrypt(strToEncrypt.trim());
                System.out.println("String to Encrypt : " + strToEncrypt);
                System.out.println("Encrypted : " + encryptedStr);
                final String strToDecrypt = encryptedStr;
                final String decryptedStr = CipherUtils.decrypt(strToDecrypt.trim());
                System.out.println("String To Decrypt : " + strToDecrypt);
                System.out.println("Decrypted : " + decryptedStr);
        }
        catch (Exception e)
        {
            System.out.println("Error while parsing command " + e);
        }

    }
}
