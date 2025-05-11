<?php
session_start();
error_reporting(0);
include('includes/config.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<title>TMS | Tourism Management System</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script type="applijewelleryion/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<link href="css/style.css" rel='stylesheet' type='text/css' />
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,700,600' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,700,300' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
<link href="css/font-awesome.css" rel="stylesheet">
<!-- Custom Theme files -->
<script src="js/jquery-1.12.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<!--animate-->
<link href="css/animate.css" rel="stylesheet" type="text/css" media="all">
<script src="js/wow.min.js"></script>
	<script>
		 new WOW().init();
	</script>
<!--//end-animate-->
</head>
<body>
<?php include('includes/header.php');?>
<div class="banner">
	<div class="container">
		<!-- <h1 class="wow zoomIn animated animated" data-wow-delay=".5s" style="visibility: visible; animation-delay: 0.5s; animation-name: zoomIn;" style="color:#000 !important"> TMS - Tourism Management System</h1> -->
	</div>
</div>






<!---holiday---->
<div class="container">
	<div class="holiday">
	



	
	<h3>Package List</h3>

					
<?php $sql = "SELECT * from tbltourpackages order by rand() limit 4";
$query = $dbh->prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{	?>
			<div class="rom-btm">
				<div class="col-md-3 room-left wow fadeInLeft animated" data-wow-delay=".5s">
					<img src="admin/pacakgeimages/<?php echo htmlentities($result->PackageImage);?>" class="img-responsive" alt="">
				</div>
				<div class="col-md-6 room-midle wow fadeInUp animated" data-wow-delay=".5s">
					<h4>Package Name: <?php echo htmlentities($result->PackageName);?></h4>
					<h6>Package Type : <?php echo htmlentities($result->PackageType);?></h6>
					<p><b>Package Location :</b> <?php echo htmlentities($result->PackageLocation);?></p>
					<p><b>Features</b> <?php echo htmlentities($result->PackageFetures);?></p>
				</div>
				<div class="col-md-3 room-right wow fadeInRight animated" data-wow-delay=".5s">
					<h5>Rs. <?php echo htmlentities($result->PackagePrice);?></h5>
					<a href="package-details.php?pkgid=<?php echo htmlentities($result->PackageId);?>" class="view">Details</a>
				</div>
				<div class="clearfix"></div>
			</div>

<?php }} ?>
			
		
<div><a href="package-list.php" class="view">View More Packages</a></div>
</div>
			<div class="clearfix"></div>
	</div>



<!--- routes ---->
<div class="routes">
	<div class="container">
		<div class="col-md-4 routes-left wow fadeInRight animated" data-wow-delay=".5s">
			<div class="rou-left">
				<a href="#"><i class="glyphicon glyphicon-list-alt"></i></a>
			</div>
			<div class="rou-rgt wow fadeInDown animated" data-wow-delay=".5s">
				<h3>80000</h3>
				<p>Enquiries</p>
			</div>
				<div class="clearfix"></div>
		</div>
		<div class="col-md-4 routes-left">
			<div class="rou-left">
				<a href="#"><i class="fa fa-user"></i></a>
			</div>
			<div class="rou-rgt">
				<h3>1900</h3>
				<p>Registered users</p>
			</div>
				<div class="clearfix"></div>
		</div>
		<div class="col-md-4 routes-left wow fadeInRight animated" data-wow-delay=".5s">
			<div class="rou-left">
				<a href="#"><i class="fa fa-ticket"></i></a>
			</div>
			<div class="rou-rgt">
				<h3>7,00,00,000+</h3>
				<p>Booking</p>
			</div>
				<div class="clearfix"></div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>

<?php include('includes/footer.php');?>
<!-- signup -->
<?php include('includes/signup.php');?>			
<!-- //signu -->
<!-- signin -->
<?php include('includes/signin.php');?>			
<!-- //signin -->
<!-- write us -->
<?php include('includes/write-us.php');?>			
<!-- //write us -->

<!-- Chatbot -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    .chat-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 500px;
        min-width: 400px;
        max-width: 90vw;
        height: 600px;
        min-height: 400px;
        max-height: 80vh;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        z-index: 1000;
        transition: all 0.3s ease;
        resize: both;
    }
    
    .chat-header {
        background: linear-gradient(135deg, #4e54c8, #8f94fb);
        color: white;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        cursor: move;
    }
    
    .chat-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }
    
    .chat-header .close-btn {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background-color 0.3s;
    }
    
    .chat-header .close-btn:hover {
        background-color: rgba(255,255,255,0.2);
    }
    
    .chat-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background-color: #f8f9fa;
    }
    
    .message {
        margin-bottom: 15px;
        max-width: 90%;
        clear: both;
        word-wrap: break-word;
        white-space: normal;
    }
    
    .user-message {
        float: right;
        background-color: #4e54c8;
        color: white;
        padding: 10px 15px;
        border-radius: 18px 18px 0 18px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .bot-message {
        float: left;
        background-color: white;
        color: #333;
        padding: 10px 15px;
        border-radius: 18px 18px 18px 0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .bot-message pre {
        white-space: normal;
        font-family: inherit;
        margin: 0;
        overflow-wrap: break-word;
    }
    
    .chat-input {
        padding: 15px;
        background-color: white;
        border-top: 1px solid #eee;
        display: flex;
        align-items: center;
    }
    
    .chat-input input {
        flex: 1;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 20px;
        outline: none;
        font-size: 14px;
        transition: border-color 0.3s;
    }
    
    .chat-input input:focus {
        border-color: #4e54c8;
    }
    
    .chat-input button {
        background: linear-gradient(135deg, #4e54c8, #8f94fb);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-left: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s;
    }
    
    .chat-input button:hover {
        transform: scale(1.05);
    }
    
    .chat-toggle {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #4e54c8, #8f94fb);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 1001;
        transition: transform 0.3s;
    }
    
    .chat-toggle:hover {
        transform: scale(1.1);
    }
    
    .chat-toggle i {
        font-size: 24px;
    }
    
    .quick-questions {
        padding: 10px 15px;
        background-color: #f0f2ff;
        border-top: 1px solid #eee;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .quick-questions button {
        background-color: white;
        color: #4e54c8;
        border: 1px solid #4e54c8;
        padding: 8px 12px;
        border-radius: 15px;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s;
        white-space: nowrap;
    }
    
    .quick-questions button:hover {
        background-color: #4e54c8;
        color: white;
    }
    
    .section-header {
        font-weight: bold;
        margin-top: 10px;
        color: #4e54c8;
        display: block;
    }
    
    .rating {
        color: #ffc107;
        font-size: 1.1em;
    }
    
    .separator {
        border-top: 1px dashed #ddd;
        margin: 10px 0;
    }
    
    @media (max-width: 768px) {
        .chat-container {
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
            bottom: 0;
            right: 0;
            border-radius: 0;
        }
    }
</style>

<div class="chat-toggle" onclick="toggleChat()">
    <i class="fa fa-comments"></i>
</div>

<div class="chat-container" id="chatContainer" style="display: none;">
    <div class="chat-header">
        <h3><i class="fa fa-robot"></i> Travel Assistant</h3>
        <button class="close-btn" onclick="toggleChat()">&times;</button>
    </div>
    <div class="chat-messages" id="chatMessages">
        <div class="message bot-message">
            <pre>👋 Hello! I'm your travel assistant. How can I help you today?</pre>
        </div>
    </div>
    <div class="quick-questions">
        <h4>Quick Questions:</h4>
        <button onclick="quickAsk('Show me available tour packages')">Tour Packages</button>
        <button onclick="quickAsk('Check my booking status')">Booking Status</button>
        <button onclick="quickAsk('Check my enquiry status')">Enquiry Status</button>
        <button onclick="quickAsk('Check my issue status')">Issue Status</button>
        <button onclick="quickAsk('Show me FAQs')">FAQs</button>
        <button onclick="window.location.href='submit-package.php'">Add New Package</button>
    </div>
    <div class="chat-input">
        <input type="text" id="userInput" placeholder="Type your message here..." onkeypress="handleKeyPress(event)">
        <button onclick="sendMessage()"><i class="fa fa-paper-plane"></i></button>
    </div>
</div>

<script>
    // Make the chat window draggable
    function makeDraggable(element) {
        let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
        const header = element.querySelector('.chat-header');
        
        header.onmousedown = dragMouseDown;

        function dragMouseDown(e) {
            e.preventDefault();
            pos3 = e.clientX;
            pos4 = e.clientY;
            document.onmouseup = closeDragElement;
            document.onmousemove = elementDrag;
        }

        function elementDrag(e) {
            e.preventDefault();
            pos1 = pos3 - e.clientX;
            pos2 = pos4 - e.clientY;
            pos3 = e.clientX;
            pos4 = e.clientY;
            element.style.top = (element.offsetTop - pos2) + "px";
            element.style.left = (element.offsetLeft - pos1) + "px";
        }

        function closeDragElement() {
            document.onmouseup = null;
            document.onmousemove = null;
        }
    }

    function toggleChat() {
        const chatContainer = document.getElementById('chatContainer');
        if (chatContainer.style.display === 'none') {
            chatContainer.style.display = 'flex';
            // Initialize draggable when showing chat
            makeDraggable(chatContainer);
        } else {
            chatContainer.style.display = 'none';
        }
    }
    
    function handleKeyPress(event) {
        if (event.key === 'Enter') {
            sendMessage();
        }
    }
    
    function sendMessage() {
        const messageInput = document.getElementById('userInput');
        const message = messageInput.value.trim();
        
        if (message === '') return;
        
        // Add user message to chat
        addMessage(message, 'user');
        messageInput.value = '';
        
        // Show typing indicator
        const typingIndicator = document.createElement('div');
        typingIndicator.className = 'message bot-message';
        typingIndicator.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';
        document.getElementById('chatMessages').appendChild(typingIndicator);
        
        // Scroll to bottom
        document.getElementById('chatMessages').scrollTop = document.getElementById('chatMessages').scrollHeight;
        
        // Send message to server
        fetch('chatbot.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'message=' + encodeURIComponent(message)
        })
        .then(response => response.json())
        .then(data => {
            // Remove typing indicator
            document.getElementById('chatMessages').removeChild(typingIndicator);
            
            // Add bot response
            addMessage(data.response, 'bot');
        })
        .catch(error => {
            console.error('Error:', error);
            // Remove typing indicator
            document.getElementById('chatMessages').removeChild(typingIndicator);
            addMessage("I'm sorry, I'm having trouble connecting right now. Please try again later.", 'bot');
        });
    }
    
    function addMessage(message, sender) {
        const chatMessages = document.getElementById('chatMessages');
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message ' + sender + '-message';
        
        // Format the message with proper line breaks and styling
        const formattedMessage = formatMessage(message);
        messageDiv.innerHTML = formattedMessage;
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    function formatMessage(message) {
        // Replace line breaks with <br> tags
        let formatted = message.replace(/\n/g, '<br>');
        
        // Add styling for section headers
        formatted = formatted.replace(/([A-Z\s]+):/g, '<span class="section-header">$1:</span>');
        
        // Add styling for ratings
        formatted = formatted.replace(/(★+☆*)/g, '<span class="rating">$1</span>');
        
        // Add styling for separators
        formatted = formatted.replace(/(━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━)/g, '<div class="separator">$1</div>');
        
        return '<pre>' + formatted + '</pre>';
    }
    
    function quickAsk(question) {
        document.getElementById('userInput').value = question;
        sendMessage();
    }

    // Initialize draggable when page loads
    window.onload = function() {
        const chatContainer = document.getElementById('chatContainer');
        makeDraggable(chatContainer);
    };
</script>
</body>
</html>