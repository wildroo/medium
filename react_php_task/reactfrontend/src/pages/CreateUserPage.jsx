import React, { useState } from 'react';
import getToken from '../api_client/auth';
import { uploadImage } from '../api_client/files';
import { createUser } from '../api_client/user';
import '../App.css';

const CreateUserPage = () => {
    const [publicName, setPublicName] = useState("");
    const [password, setPassword] = useState("");    
    const [email, setEmail] = useState("");

    const [fileUrl, setFileUrl] = useState("");

    const [error, setError] = useState("");

    const dismissError = () => {
        setError("");
    }

    const handleSubmit = async (event) => {
        event.preventDefault();

        if (!email) {
            setError("Email is required");
        }
        else if (!publicName) {
            setError("Please provide your public name");
        }
        else if (!password) {
            setError("Please enter the password");
        }
        else {
            setError("");
            await submitDetailsToBackend();
        }        
    }

    const onFileChange = event => { 
        setFileUrl(event.target.files[0]); 
    }; 
            
    const submitDetailsToBackend = async () => {
        const uid = await createUser(email, password, publicName);

        if(!uid){
            setError("Couldn't create a new user.");
            return;
        }
        
        if(fileUrl){
            const token = await getToken(uid);
            await uploadImage(token, fileUrl);
        }
    }

    return (
        <div className="CreateUserForm">
            <form onSubmit={handleSubmit}>
                {
                    error &&
                    <h3 data-test="error" onClick={dismissError}>
                        <button onClick={dismissError}>✖</button>
                        {error}
                    </h3>
                }

                <label>Email</label>
                <input type="text" data-test="username" value={email} onChange={(e) => setEmail(e.target.value)} />

                <label>Public Name</label>
                <input type="text" data-test="username" value={publicName} onChange={(e) => setPublicName(e.target.value)} />

                <label>Password</label>
                <input type="password" data-test="password" value={password} onChange={(e) => setPassword(e.target.value)} />

                <label>Your Picture</label>
                <input type="file" onChange={onFileChange} />                 

                <input type="submit" value="Log In" data-test="submit" />
            </form>
        </div>
    );
}

export default CreateUserPage;