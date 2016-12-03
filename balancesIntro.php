<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<?php include 'head.php'; ?>
<title>
Introducing Balances
</title>
</head>
<body>
<?php include 'frame.php';?>
<h3>Order System has changed</h3>
<p>Due to the change problem that occurred while paying order's money, we have added a new feature 
to Order System. Now every user has a balance that can be charged and orders that user makes will
be paid from their balance. Every user can make an initial payment (for example 20L.E.) and it 
will appear in their balance. When users get their food, they have the option to pay from their
balance instead of paying by cash. This will help making orders easier and quicker. Users can 
trace their money as every payment or charging balance is recorded as a transaction and users
can view their transactions to verify the everything is OK. Users can still pay by cash and 
ignore this new feature, but this is not recommended. Users can charge their balances by giving
money to Saeed or to Alaa. Money transactions will be available in My Options page.</p>
<p class=arabic >نظرا لوجود مشكلة في الفكة أثناء دفع ثمن الأوردر تم إضافة خاصية جديدة. تم عمل لكل مستخدم حساب يمكن إيداع مبلغ فيه واستخدامه لدفع ثمن الأكل المطلوب. يمكن للمستخدم أن يودع مبلغ مبدئي (مثال 20 جنيه) 
وتضاف إلى رصيده وعند دفع حساب الأكل يمكنه خصم حساب الأكل من رصيده.وهذا يسهل عمل الطلبات ويجعلها أسرع
ويمكن للمستخدم أن يتابع حسابه عن طريق حركات الإيداع والدفع. هذه الخاصية ليست إجبارية ويمكن تجاهلها
ودفع ثمن الأكل عند استلامه. يمكن دفع مبلغ الإيداع لسعيد أو علاء
يمكن عرض الحركات في صفحة My Options </p> 
<?php include 'frameend.php';?>
</body>
</html>