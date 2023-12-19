@component('mail::message')

<center>Beste collega, 
    
<br>
Voordat je kan gebruikmaken van het 'HSP' webapplicatie. 
<br>
Moet je erste een wachtwoord aanmaken. Dus neem even de tijd om  
<br> 
klik hieronder om</center>

@component('mail::button', ['url' => $url])
je account te activeren
@endcomponent
<br>
Met vriendelijke groet,
<br>Hotelkamerschoonmaakplan
@endcomponent
