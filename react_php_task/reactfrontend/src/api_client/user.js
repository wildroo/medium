import Axios from "axios";
import { API_URL } from "../models/constants";

export const createUser = async (email, password, publicName) => {
    const request = {
        email: email,
        password: password,
        publicName: publicName
    };

    return await Axios({
            method: 'POST',
            url: API_URL + 'user/index.php?action=createUser',
            data: request,
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(function (response) {
            return response.data.uid;
        })
        .catch(function (response) {            
            console.error("Status::: " + response.status + ". Bad response ::: " + response);
            return null;
        });        
}

export const changePassword = async (token, uid, newPwd) => {
    const request = {
        uid: uid,
        newPassword: Buffer.from(newPwd).toString('base64')
    };

    return await Axios({
            method: 'POST',
            url: API_URL + 'user/index.php?action=changePassword',
            data: request,
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(function (response) {
            return true;
        })
        .catch(function (response) {            
            console.error("Status::: " + response.status + ". Bad response ::: " + response);
            return false;
        });        
}