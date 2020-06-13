package com.magenest.geoquiz;

import android.app.Activity;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

public class QuizActivity extends Activity {
private Button mTrueButton;
    private Button mFalseButton;

    private Button mNextButton;

    private TextView mQuestionTextView;

    private TrueFalse[] mTrueFalseBank = new TrueFalse[] {
        new TrueFalse(R.string.question_japan , true) ,
        new TrueFalse(R.string.question_china , false) ,
        new TrueFalse(R.string.question_thailand , false)
    };


    private  int mCurrentIndex = 1;

    private void checkAnswer(boolean answer) {
        boolean isTrueAnswer = false;
        if ( mTrueFalseBank[mCurrentIndex].ismTrueQuuestion() == answer) {
            isTrueAnswer = true;
        }

        if (isTrueAnswer)  {
            Toast.makeText(QuizActivity.this , R.string.correct_toast,Toast.LENGTH_SHORT);
        } else {
            Toast.makeText(QuizActivity.this, R.string.incorrect_toast , Toast.LENGTH_SHORT);
        }


    }
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_quiz);
        mTrueButton = (Button)findViewById(R.id.true_button);
        mFalseButton = (Button)findViewById(R.id.false_button);

        mQuestionTextView = (TextView)findViewById(R.id.question_text_view);

        mTrueButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
               checkAnswer(true);

            }
        });

        mFalseButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                checkAnswer(false);
            }


        });

        mNextButton = (Button)findViewById(R.id.next_button);
        mNextButton.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {
                mCurrentIndex = (mCurrentIndex + 1) % 3;


                mQuestionTextView.setText( mTrueFalseBank[mCurrentIndex].getmQuestion());
            }
        });
        int question = this.mTrueFalseBank[mCurrentIndex].getmQuestion();

        mQuestionTextView.setText(question);

    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_quiz, menu);
        return true;
    }

}
