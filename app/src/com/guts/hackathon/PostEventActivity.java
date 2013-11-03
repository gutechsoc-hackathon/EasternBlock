package com.guts.hackathon;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.drawable.BitmapDrawable;
import android.os.Bundle;
import android.provider.MediaStore;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.MultiAutoCompleteTextView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;
import java.util.ArrayList;

public class PostEventActivity extends Activity {

    private int CAPTURE_IMAGE = 1;

    protected RadioGroup mRadioGroup;
    protected RadioButton mRadioEvent, mRadioWarning;
    protected MultiAutoCompleteTextView mTagTextView;

    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.post_event_form);

        setupSpinner();
        setupAutoCompleteTextView();
        setupSubmit();

        mRadioGroup = (RadioGroup) findViewById(R.id.postevent_radiogroup);
        mRadioEvent = (RadioButton) findViewById(R.id.postevent_radioevent);
        mRadioWarning = (RadioButton) findViewById(R.id.postevent_radiowarning);

        Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
        startActivityForResult(takePictureIntent, CAPTURE_IMAGE);
    }

    @SuppressLint("NewApi")
	@Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        if (resultCode == Activity.RESULT_OK) {
            if (requestCode == CAPTURE_IMAGE) {
                Bundle extras = data.getExtras();
                Bitmap img = (Bitmap) extras.get("data");
                LinearLayout layout = (LinearLayout) findViewById(R.id.postevent_layout);
                layout.setBackground(new BitmapDrawable(getResources(), img));
            }
        } else {
            finish();
        }
    }

    private void setupSpinner() {
        Spinner spinner = (Spinner) findViewById(R.id.spinner);
        ArrayAdapter<CharSequence> adapter = ArrayAdapter.createFromResource(this,
                R.array.expiration_array, android.R.layout.simple_spinner_item);
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinner.setAdapter(adapter);
    }

    private void setupAutoCompleteTextView() {
        mTagTextView = (MultiAutoCompleteTextView) findViewById(R.id.multiAutoCompleteTextView);
        ArrayList<String> tags = new ArrayList<String>();
        for (int i = 0; i < 3; i++) {
            tags.add("Avocado" + i);
            tags.add("Apple" + i);
            tags.add("Banana" + i);
        }
        ArrayAdapter<String> adapter = new ArrayAdapter<String>(this, android.R.layout.simple_list_item_1, tags);
        mTagTextView.setAdapter(adapter);
        mTagTextView.setTokenizer(new MultiAutoCompleteTextView.CommaTokenizer());
    }

    private void setupSubmit() {
        Button button = (Button) findViewById(R.id.submitButton);
        button.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {

                mRadioWarning.setError(null);

                Boolean cancel = false;
                if (mRadioGroup.getCheckedRadioButtonId() < 0) {
                    mRadioWarning.setError("");
                    cancel = true;
                }

                if (!cancel)
                    finish();
            }
        });
    }
}
