import 'package:flutter/material.dart';
import 'package:pennywise/resources/app_colours.dart';
import 'package:pennywise/resources/app_strings.dart';
import 'package:pennywise/resources/app_styles.dart';
import 'package:pennywise/resources/views/components/ui/button.dart';

import '../../../models/slide.dart';

class WalkThroughScreen extends StatefulWidget {
  const WalkThroughScreen({super.key});

  @override
  State<WalkThroughScreen> createState() => _WalkThroughScreenState();
}

class _WalkThroughScreenState extends State<WalkThroughScreen> {
  PageController pageController = new PageController();

  // create a list of slides for the walkthrough screen
  List<SlideModel> slides = [
    SlideModel(AppStrings.walkthroughTitle1, AppStrings.walkthroughDescription1,
        "assets/images/walkthrough1.png"),
    SlideModel(AppStrings.walkthroughTitle2, AppStrings.walkthroughDescription2,
        "assets/images/walkthrough2.png"),
    SlideModel(AppStrings.walkthroughTitle3, AppStrings.walkthroughDescription3,
        "assets/images/walkthrough3.png")
  ];

  int currentPageIndex = 0;
  @override
  Widget build(BuildContext context) {
    return Scaffold(
        backgroundColor: AppColours.bgColor,
        body: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            Expanded(
                child: pages() ),
            const SizedBox(height: 24),
            Padding(
              padding: const EdgeInsets.all(24),
              child: Column(
                children: [
                  indicators(),
                  const SizedBox(height: 24),
                  buttons(),
                ],
              ),
            )
          ],
        ));
  }

  Widget indicators() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        for (int i = 0; i < slides.length; i += 1) ...[
          //navigate user to slide when indicator is clicked
          InkWell(
            onTap: () {
              if (i != currentPageIndex)
                pageController.animateToPage(i,
                    duration: const Duration(milliseconds: 500),
                    curve: Curves.easeInOut);
            },
            child: Icon(Icons.circle,
                size: currentPageIndex == i ? 16 : 8,
                color: currentPageIndex == i
                    ? AppColours.primaryColour
                    : AppColours.primaryColourLight),
          ),
          if (i < slides.length - 1) const SizedBox(width: 8)
        ]
      ],
    );
  }

  Widget buttons(){
    return Column(
      children: [
        ButtonComponent(
          label: AppStrings.signUp,
        ),
        const SizedBox(height: 24),
        ButtonComponent(
          type: ButtonType.secondary,
          label: AppStrings.login,
        ),
      ],
    );
  }

  Widget pages(){
    return PageView.builder(
      controller: pageController,
      itemBuilder: (context, index) {
        return ListView(
          shrinkWrap: true,
          padding: const EdgeInsets.all(24),
          children: [
            const SizedBox(height: 48),
            Center(
              child: Image.asset(slides[index].image,width: MediaQuery.of(context).size.width/1.5,),
            ),
            // define space
            const SizedBox(height: 24),
            Text(slides[index].title,
                style: AppStyles.title1(), textAlign: TextAlign.center),
            const SizedBox(height: 16),
            Text(slides[index].description,
                style: AppStyles.regular1(color: AppColours.light20,weight:FontWeight.w500),
                textAlign: TextAlign.center),
          ],
        );
      },
      itemCount: slides.length,
      onPageChanged: (index) =>
          setState(() => currentPageIndex = index),
    );
  }
}
