import './bootstrap';
import Search from './live-search';
import Chat from './chat';

if(document.querySelector("#q")){
    new Search();
    
    }

    if(document.querySelector(".header-chat-icon")){
        new Chat();
        
        }
    
