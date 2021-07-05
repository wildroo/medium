import Axios from "axios";
import { API_URL } from "../models/constants";

const getToken = async (uid) => {
    await Axios({
            method: 'get',
            url: API_URL + 'auth.php?uid=' + uid,
        })
        .then(function (response) {
            return response.data;
        })
        .catch(function (response) {            
            throw Error("Status::: " + response.status + ". Bad response ::: " + response);
        });    
}

export default getToken;