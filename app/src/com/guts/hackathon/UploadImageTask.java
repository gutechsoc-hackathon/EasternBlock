package com.guts.hackathon;

import java.io.BufferedReader;
import java.io.ByteArrayOutputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;

import javax.security.auth.PrivateCredentialPermission;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.mime.HttpMultipartMode;
import org.apache.http.entity.mime.MultipartEntity;
import org.apache.http.entity.mime.content.ByteArrayBody;
import org.apache.http.entity.mime.content.StringBody;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;

import android.graphics.Bitmap;
import android.graphics.Bitmap.CompressFormat;
import android.os.AsyncTask;
import android.util.Log;

public class UploadImageTask extends AsyncTask<Object, Void, String>{
	private ResponseCallback done;
	@Override
	protected String doInBackground(Object... params) {

		

		String resp = null;
		try {
			Bitmap bm = (Bitmap) params[0];
			String image_name = (String) params[1];

			ByteArrayOutputStream bos = new ByteArrayOutputStream();

			bm.compress(CompressFormat.PNG, 100, bos);

			byte[] data = bos.toByteArray();

			HttpClient httpClient = new DefaultHttpClient();
			//TODO
			HttpPost postRequest = new HttpPost("shacron.twilightparadox.com/index.php");

			ByteArrayBody bab = new ByteArrayBody(data, image_name);
			
			MultipartEntity reqEntity = new MultipartEntity(HttpMultipartMode.BROWSER_COMPATIBLE);
			reqEntity.addPart("r", new StringBody("media/upload"));
			reqEntity.addPart("sess_id", new StringBody(ThisUser.session));
			reqEntity.addPart("type_id", new StringBody("1"));
			reqEntity.addPart("event_id", new StringBody((String)params[2]));			
			reqEntity.addPart("file", bab);

			postRequest.setEntity(reqEntity);
			HttpResponse response = httpClient.execute(postRequest);
			BufferedReader reader = new BufferedReader(new InputStreamReader(response.getEntity().getContent(), "UTF-8"));
			String sResponse;
			StringBuilder s = new StringBuilder();
			while ((sResponse = reader.readLine()) != null) {
				s = s.append(sResponse);
			}
			resp=s.toString();
		} catch (Exception e) {
			// handle exception here
			Log.e(e.getClass().getName(), e.getMessage());
		}
		return resp;


	}

	public void setDone(ResponseCallback d) {
		done = d;
	}

	@Override
	protected void onPostExecute(String r) {
		done.processResponse(r);
	}

}
