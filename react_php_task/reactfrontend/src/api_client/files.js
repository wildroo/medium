import Axios from "axios";
import { API_URL } from "../models/constants";

export const uploadImage = async (token, imageUrl) => {
    
    const fileBlob = await urlToObject(imageUrl);

    var bodyFormData = new FormData();
    bodyFormData.append('file', fileBlob);

    await Axios({
            method: 'post',
            url: API_URL + 'files/index.php',
            data: bodyFormData,
            headers: {
                'Content-Type': 'multipart/form-data', 
                'Authorization' : token,
                'Crossorigin' : 'true'
            }
        })
        .then(function (response) {
            console.log(response);
        })
        .catch(function (response) {
            console.log(response);
        });
}

//If you need to convert an URL with a pic to file object
const urlToObject = async (imageUrl) => {
    const response = await fetch(imageUrl);    
    const blob = await response.blob();
    const file = new File([blob], 'image.jpg', { type: blob.type });
    return file;
}