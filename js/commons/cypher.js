function CryptoJSAesDecrypt(passphrase,encrypted_string){

    var obj_json = JSON.parse(CryptoJS.enc.Base64.parse(encrypted_string).toString(CryptoJS.enc.Utf8));

    var encrypted = obj_json.ciphertext;
    var salt = CryptoJS.enc.Hex.parse(obj_json.salt);
    var iv = CryptoJS.enc.Hex.parse(obj_json.iv);   

    var key = CryptoJS.PBKDF2(passphrase, salt, { hasher: CryptoJS.algo.SHA512, keySize: 64/8, iterations: 999});


    var decrypted = CryptoJS.AES.decrypt(encrypted, key, { iv: iv});

    return decrypted.toString(CryptoJS.enc.Utf8);
}

function CryptoJSAesEncrypt(passphrase, plain_text){

    var salt = CryptoJS.lib.WordArray.random(256);
    var iv = CryptoJS.lib.WordArray.random(16);
    //for more random entropy can use : https://github.com/wwwtyro/cryptico/blob/master/random.js instead CryptoJS random() or another js PRNG

    var key = CryptoJS.PBKDF2(passphrase, salt, { hasher: CryptoJS.algo.SHA512, keySize: 64/8, iterations: 999 });

    var encrypted = CryptoJS.AES.encrypt(plain_text, key, {iv: iv});

    var data = {
        ciphertext : CryptoJS.enc.Base64.stringify(encrypted.ciphertext),
        salt : CryptoJS.enc.Hex.stringify(salt),
        iv : CryptoJS.enc.Hex.stringify(iv)    
    }

    return btoa(JSON.stringify(data));
}
