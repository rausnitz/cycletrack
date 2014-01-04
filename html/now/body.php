<body>
<div id='results'>

<h2>
<span id='stationheader'><?php echo $stationName; ?></span>
</h2>

<h4><?php echo date('g:i a', time()); ?><br />
<?php echo date('l, M j', time()); ?></h4>

<div align='center' style='margin-top:20px;'>

<table cellpadding='3'>
<tr>
    <td></td>
    <td>&nbsp;Bikes&nbsp;</td>
    <td>&nbsp;Docks&nbsp;</td>
</tr>
<tr id='now'>
    <td>Now</td>
    <td><?php echo $stationBikes; ?></td>
    <td><?php echo $stationDocks; ?></td>
</tr>

<tr>
    <td><?php echo date('g:i a', $row1[4]); ?></td>
    <td><?php echo $row1[2] ?></td>
    <td><?php echo $row1[3] ?></td>
</tr>

<tr>
    <td><?php echo date('g:i a', $row2[4]); ?></td>
    <td><?php echo $row2[2] ?></td>
    <td><?php echo $row2[3] ?></td>
</tr>


<tr>
    <td><?php echo date('g:i a', $row3[4]); ?></td>
    <td><?php echo $row3[2] ?></td>
    <td><?php echo $row3[3] ?></td>
</tr>
</table>

<p style='margin-top:5%;'>
<a href='javascript:window.location.href=window.location.href'>refresh</a>
&nbsp;&#8226;&nbsp;
<a href='<?php echo $iqURL ?>'>go back</a>
</p>
</div>

</div>
</body></html>