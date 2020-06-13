package com.magenest.geoquiz;

/**
 * Created by root on 13/09/2015.
 */
public class TrueFalse {
    private int mQuestion;

    private boolean mTrueQuuestion;


    public int getmQuestion() {
        return mQuestion;
    }

    public void setmQuestion(int mQuestion) {
        this.mQuestion = mQuestion;
    }


    public boolean ismTrueQuuestion() {
        return mTrueQuuestion;
    }

    public void setmTrueQuuestion(boolean mTrueQuuestion) {
        this.mTrueQuuestion = mTrueQuuestion;
    }


    public TrueFalse(int question, boolean trueQuestion) {
        this.mQuestion = question;
        this.mTrueQuuestion = trueQuestion;

    }
}
