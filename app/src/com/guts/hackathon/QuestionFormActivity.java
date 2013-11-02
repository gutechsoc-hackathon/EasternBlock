package com.guts.hackathon;

import android.app.Activity;
import android.os.Bundle;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.MultiAutoCompleteTextView;
import android.widget.Spinner;

import java.util.ArrayList;

public class QuestionFormActivity extends Activity {
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.questionform);

        setupSpinner();
        setupAutoCompleteTextView();
        setupSubmit();
    }

    private void setupSpinner() {
        Spinner spinner = (Spinner) findViewById(R.id.spinner);
        // Create an ArrayAdapter using the string array and a default spinner layout
        ArrayAdapter<CharSequence> adapter = ArrayAdapter.createFromResource(this,
                R.array.expiration_array, android.R.layout.simple_spinner_item);
        // Specify the layout to use when the list of choices appears
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        // Apply the adapter to the spinner
        spinner.setAdapter(adapter);
    }

    private void setupAutoCompleteTextView() {
        // Get a reference to the AutoCompleteTextView in the layout
        MultiAutoCompleteTextView textView = (MultiAutoCompleteTextView) findViewById(R.id.multiAutoCompleteTextView);
        // Get the string array
        ArrayList<String> tags = new ArrayList<String>();
        for (int i = 0; i < 3; i++) {
            tags.add("Avocado" + i);
            tags.add("Apple" + i);
            tags.add("Banana" + i);
        }
        // Create the adapter and set it to the AutoCompleteTextView
        ArrayAdapter<String> adapter = new ArrayAdapter<String>(this, android.R.layout.simple_list_item_1, tags);
        textView.setAdapter(adapter);
        textView.setTokenizer(new MultiAutoCompleteTextView.CommaTokenizer());
    }

    private void setupSubmit() {
        Button button = (Button) findViewById(R.id.submitButton);
        button.setOnClickListener(new View.OnClickListener() {
            public void onClick(View v) {
                finish();
            }
        });
    }
}
