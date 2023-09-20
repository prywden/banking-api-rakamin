## Tugas Backend Development

#### [API link](https://perbankan-api.azurewebsites.net/)

#### API Method
1. Account balance
2. Account profile
3. Transactions history
4. Filter transactions history (Minimum transfer amount)
5. Transfer to another account

***

#### GET Request example
* /api.php/transactions?userId=1
* /api.php/profile?userId=1
* /api.php/balance?userId=1
* /api.php/transactions/filter?minTransferAmount=10000&userId=1


#### POST Request example
* With Content-Type: application/x-www-form-urlencoded
  * endpoint: /api.php/transfer
  * param: senderAccount=1&receiverAccount=9&amount=90000&senderAccountPin=248719


#### Author
* [Ahmad Zubaid Muzzakki](https://github.com/laqqueta)