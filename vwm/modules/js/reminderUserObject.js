/**
 * 
 * reminder User Object
 * 
 * @returns {null}
 */
function ReminderUser() {
    var self = this;
    var email = null;
    var reminderUserId = null;
    var userId = null
    
    // Private members are made by the constructor	
    var constructor = function() {
        var email = null;
        var reminderUserId = null;
        var userId = null
        
        //setters
        self.setEmail = function(userEmail){
            email = userEmail;
        }
        self.setReminderId = function(id){
            reminderUserId = id;
        }
        self.setUserId = function(id){
            userId = id;
        }
        
        //getters
        self.getEmail = function(){
            return email;
        }
        
        self.getReminderId = function(){
            return reminderUserId;
        }
        
        self.getUserId = function(){
            return userId;
        }
        //function for getting attributes
        self.getAttributes = function() {
            var reminderUsersAttributes = {
                temporaryId: self.getReminderId(),
                email: self.getEmail(),
                userId: self.getUserId()
            }
            return reminderUsersAttributes;
        }

        //Convert Wastes to JSON format string
        self.toJson = function() {
            var reminderUsersAttributes = self.getAttributes();
            var encoded = $.toJSON(reminderUsersAttributes);
            
            return encoded;
        }
    }
    
    constructor();

}